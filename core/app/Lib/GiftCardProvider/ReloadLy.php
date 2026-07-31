<?php

namespace App\Lib\GiftCardProvider;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\ApiProvider;
use App\Models\Transaction;
use Exception;

class ReloadLy
{
    private $baseURL = 'https://giftcards.reloadly.com';
    public  $apiConfig;

    public function __construct()
    {
        $apiConfig = ApiProvider::where('provider', 'reloadly_api')->first();

        if (!$apiConfig) {
            throw new Exception("API configuration not found.");
        }

        $this->apiConfig = $apiConfig;
        $environment     = collect($this->apiConfig->config)->where('name', 'environment')->first();

        if (!$environment) {
            throw new Exception('API configuration not found.');
        }

        if ($environment->value == 'production') {
            $this->baseURL = "https://giftcards.reloadly.com";
        } else {
            $this->baseURL = "https://giftcards-sandbox.reloadly.com";
        }
    }

    public function getHeaders()
    {

        $accessToken    = collect($this->apiConfig->access_token)->where('name', 'gift_card_token')->first();
        $tokenValue     = @$accessToken->value;
        $tokenExpiredAt = $accessToken->expired_at ? now()->parse($accessToken->expired_at) : now();


        if (!$tokenValue || !$tokenExpiredAt ||  $tokenExpiredAt < now() || is_null($tokenExpiredAt)) {
            $this->createNewToken();
        }

        $apiConfig       = $this->apiConfig;
        $accessToken     = $apiConfig->access_token->gift_card_token->value;
        $accessTokenType = $apiConfig->access_token->gift_card_token->token_type;

        return [
            "Accept: application/com.reloadly.giftcards-v1+json",
            "Authorization: $accessTokenType $accessToken"
        ];
    }

    public function createNewToken()
    {
        $apiConfig   = $this->apiConfig;
        $credentials = collect($apiConfig->config ?? []);

        if ($credentials->isEmpty()) {
            throw new Exception('API configuration not found.');
        }

        $clientId     = $credentials->where('name', 'client_id')?->first()?->value;
        $clientSecret = $credentials->where('name', 'client_secret')?->first()?->value;

        if (!$clientId || !$clientSecret) {
            throw new Exception('API configuration not found.');
        }

        $authURL = 'https://auth.reloadly.com/oauth/token';

        $data = json_encode([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => 'client_credentials',
            'audience'      => $this->baseURL,
        ]);

        $headers  = ['Content-Type: application/json'];
        $response = CurlRequest::curlPostContent($authURL, $data, $headers);
        $response = json_decode($response);

        if (!@$response->access_token) {
            throw new Exception('Failed to get access token from Reloadly.');
        }

        $accessToken                              = $apiConfig->access_token;
        $accessToken->gift_card_token->token_type = $response->token_type;
        $accessToken->gift_card_token->value      = $response->access_token;
        $accessToken->gift_card_token->expired_at = now()->addSeconds($response->expires_in);
        $apiConfig->access_token                  = $accessToken;
        $apiConfig->save();

        $this->apiConfig = $apiConfig;
    }

    public function getCategories()
    {
        $url     = $this->baseURL . '/product-categories';
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);
        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function getCountries()
    {
        $url = $this->baseURL . '/countries';

        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function getProducts($iso)
    {
        $url = rtrim($this->baseURL, '/') . '/countries/' . urlencode($iso) . '/products';
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);
        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);
        return $responseData;
    }

    public function sendGiftCard($details)
    {
        $data = json_encode([
            'productId'      => $details['product_id'],
            'unitPrice'      => $details['amount'],
            'quantity'       => $details['quantity'],
            'recipientEmail' => $details['email'],
            'senderName'     => $details['name']
        ]);

        $url     = $this->baseURL . "/orders";
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $response              = CurlRequest::curlPostContent($url, $data, $headers);
        $responseContent       = $this->validateHttpResponse($response);
        $giftCardProcessStatus = strtoupper($responseContent['status']);

        $totalFee    = $responseContent['totalFee'] ?? 0.00;
        $totalAmount = $responseContent['amount'] ?? 0.00;
        $discount    = $responseContent['discount'] ?? 0.00;

        $processingStatus = true;
        $billStatus       = Status::GIFT_CARD_PENDING;
        $apiProviderTrx   = null;

        if ($giftCardProcessStatus == 'SUCCESSFUL') {
            $billStatus     = Status::GIFT_CARD_SUCCESSFUL;
            $apiProviderTrx = $responseContent['transactionId'];
        } elseif ($giftCardProcessStatus == 'PENDING') {
            $billStatus     = Status::GIFT_CARD_PENDING;
            $apiProviderTrx = $responseContent['transactionId'];
        } else {
            $processingStatus = false;
        }

        return [
            'processing_status' => $processingStatus,
            'bill_status'       => $billStatus,
            'api_provider_trx'  => $apiProviderTrx,
            'total_fee'         => $totalFee,
            'total_amount'      => $totalAmount,
            'discount'          => $discount
        ];
    }

    public function giftCardPurchaseUpdate($giftCardPurchase)
    {
        $user            = $giftCardPurchase->user;
        $giftCard        = $giftCardPurchase->giftCard;
        $headers         = $this->getHeaders();
        $url             = $this->baseURL . "/reports/transactions/$giftCardPurchase->api_provider_trx";
        $response        = CurlRequest::curlContent($url, $headers);
        $responseContent = $this->validateHttpResponse($response);

        if (!isset($responseContent['transactionId']) || !isset($responseContent['transactionId']['status'])) {
            throw new Exception("Invalid response from Reloadly: missing transaction status.");
        }

        $transaction = $responseContent['transactionId'];

        if ($transaction['status'] == 'REFUNDED') {

            $reason = $responseContent['message'] ?? "Reloadly was unable to process this gift card purchase payment. Please contact support for further assistance.";

            $giftCardPurchase->status         = Status::GIFT_CARD_REJECTED;
            $giftCardPurchase->save();

            $user->balance += $giftCardPurchase->total;
            $user->save();

            $transaction                = new Transaction();
            $transaction->user_id       = $user->id;
            $transaction->amount        = $giftCardPurchase->total;
            $transaction->post_balance  = $user->balance;
            $transaction->charge        = 0;
            $transaction->trx_type      = '+';
            $transaction->remark        = 'reject_gift_card_purchase';
            $transaction->details       = 'Rejection of gift card purchase';
            $transaction->trx           = $giftCardPurchase->trx;
            $transaction->save();

            notify($user, 'GIFT_CARD_REJECT', [
                'user'         => $user->fullname,
                'amount'       => showAmount($giftCardPurchase->total, currencyFormat: false),
                'charge'       => showAmount($giftCardPurchase->charge, currencyFormat: false),
                'product'      => $giftCard->product_name,
                'trx'          => $giftCardPurchase->trx,
                'reason'       => $reason,
                'time'         => now(),
                'post_balance' => showAmount($user->post_balance, currencyFormat: false),
            ]);

            $giftCardPurchase->status = Status::GIFT_CARD_REJECTED;
            $giftCardPurchase->save();
        }

        if ($transaction['status'] == 'SUCCESSFUL') {

            notify($user, 'GIFT_CARD_APPROVE', [
                'user'         => $user->fullname,
                'amount'       => showAmount($giftCardPurchase->total, currencyFormat: false),
                'charge'       => showAmount($giftCardPurchase->charge, currencyFormat: false),
                'product'      => $giftCard->product_name,
                'trx'          => $giftCardPurchase->trx,
                'time'         => now(),
                'post_balance' => showAmount($user->balance, currencyFormat: false),
            ]);

            $giftCardPurchase->status = Status::GIFT_CARD_SUCCESSFUL;
            $giftCardPurchase->save();
        }
    }

    public function updateGiftCardData($giftCard)
    {
        $url     = rtrim($this->baseURL, '/') . '/products/' . $giftCard->product_id;
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);
        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function validateHttpResponse($response): array
    {
        if (is_null($response) || trim($response) === '') {
            throw new Exception('Empty or null HTTP response received.');
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }



        if (isset($responseData['errorCode']) || (is_array($responseData) &&  array_key_exists('errorCode', $responseData))) {
            $message = isset($responseData['message']) && is_string($responseData['message']) ? $responseData['message'] : 'Something went wrong';
            throw new Exception(str_replace('tickets@reloadly.com', 'support', $message));
        }

        return $responseData;
    }
}
