<?php

namespace App\Lib\UtilityBillProvider;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\ApiProvider;
use App\Models\BillCategory;
use App\Models\Transaction;
use Exception;

class ReloadLy implements UtilityBillProviderInterFace
{
    private $baseURL = 'https://utilities.reloadly.com';
    private $amount;
    public  $apiConfig;

    public function __construct()
    {
        $apiConfig = ApiProvider::where('provider', 'reloadly_api')->first();

        if (!$apiConfig) {
            throw new Exception("Utility API configuration not found.");
        }

        $this->apiConfig = $apiConfig;
        $environment     = collect($this->apiConfig->config)->where('name', 'environment')->first();

        if (!$environment) {
            throw new Exception('Utility API configuration not found.');
        }

        if ($environment->value == 'production') {
            $this->baseURL = "https://utilities.reloadly.com";
        } else {
            $this->baseURL = "https://utilities-sandbox.reloadly.com";
        }
    }

    public function getHeaders()
    {


        $accessToken    = collect($this->apiConfig->access_token)->where('name', 'utility_token')->first();
        $tokenValue     = $accessToken->value;
        $tokenExpiredAt = $accessToken->expired_at ? now()->parse($accessToken->expired_at) : now();


        if (!$tokenValue || !$tokenExpiredAt ||  $tokenExpiredAt < now() || is_null($tokenExpiredAt)) {
            $this->createNewToken();
        }

        $apiConfig       = $this->apiConfig;
        $accessToken     = $apiConfig->access_token->utility_token->value;
        $accessTokenType = $apiConfig->access_token->utility_token->token_type;

        return [
            "Accept: application/com.reloadly.utilities-v1+json",
            "Authorization: $accessTokenType $accessToken"
        ];
    }

    public function createNewToken()
    {
        $apiConfig   = $this->apiConfig;
        $credentials = collect($apiConfig->config ?? []);

        if ($credentials->isEmpty()) {
            throw new Exception('Utility API configuration not found.');
        }

        $clientId     = $credentials->where('name', 'client_id')?->first()?->value;
        $clientSecret = $credentials->where('name', 'client_secret')?->first()?->value;

        if (!$clientId || !$clientSecret) {
            throw new Exception('Utility API configuration not found.');
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

        $accessToken                            = $apiConfig->access_token;
        $accessToken->utility_token->token_type = $response->token_type;
        $accessToken->utility_token->value      = $response->access_token;
        $accessToken->utility_token->expired_at = now()->addSeconds($response->expires_in);
        $apiConfig->access_token                = $accessToken;
        $apiConfig->save();

        $this->apiConfig = $apiConfig;
    }

    public function payBill($company, $details)
    {
        $data = json_encode([
            'subscriberAccountNumber' => $details->unique_id,
            'amount'                  => $details->amount,
            'amountId'                => $details->amount_id,
            'billerId'                => $company->company_id,
            'useLocalAmount'          => false,
            'referenceId'             => $details->reference,
            'additionalInfo'          => json_encode([
                'invoiceId' => null
            ])
        ]);

        $url     = $this->baseURL . "/pay";
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $response          = CurlRequest::curlPostContent($url, $data, $headers);
        $responseContent   = $this->validateHttpResponse($response);
        $billPaymentStatus = strtoupper($responseContent['status']);


        $processingStatus = true;
        $billStatus       = Status::UTILITY_BILL_PENDING;
        $apiProviderTrx   = null;

        if ($billPaymentStatus == 'SUCCESSFUL') {
            $message        = "Bill payment was successful.";
            $billStatus     = Status::UTILITY_BILL_SUCCESSFUL;
            $apiProviderTrx = $responseContent['id'];
        } elseif ($billPaymentStatus == 'PROCESSING') {
            $message        = "The bill payment is being processed.";
            $billStatus     = Status::UTILITY_BILL_PROCESSING;
            $apiProviderTrx = $responseContent['id'];
        } else {
            $message          = "Bill payment processing fail";
            $processingStatus = false;
        }

        return [
            'message'           => $message,
            'processing_status' => $processingStatus,
            'bill_status'       => $billStatus,
            'api_provider_trx'  => $apiProviderTrx
        ];
    }

    public function payBillUpdate($bill)
    {
        $user            = $bill->user;
        $company         = $bill->company;
        $headers         = $this->getHeaders();

        $url             = $this->baseURL . "/transactions/$bill->api_provider_trx";
        $response        = CurlRequest::curlContent($url, $headers);
        $responseContent = $this->validateHttpResponse($response);

        if (!isset($responseContent['transaction']) || !isset($responseContent['transaction']['status'])) {
            throw new Exception("Invalid response from Reloadly: missing transaction status.");
        }

        $transaction = $responseContent['transaction'];




        if ($transaction['status'] == 'REFUNDED') {

            $reason = $responseContent['message'] ?? "Reloadly was unable to process this utility bill payment. Please contact support for further assistance.";

            $bill->status         = Status::UTILITY_BILL_REFUNDED;
            $bill->admin_feedback = $reason;
            $bill->save();

            $user->balance += $bill->total;
            $user->save();

            $transaction                = new Transaction();
            $transaction->user_id       = $user->id;
            $transaction->amount        = $bill->total;
            $transaction->post_balance  = $user->balance;
            $transaction->charge        = 0;
            $transaction->trx_type      = '+';
            $transaction->remark        = 'reject_utility_bill';
            $transaction->details       = 'Rejection of utility bill';
            $transaction->trx           = $bill->trx;
            $transaction->save();

            notify($user, 'UTILITY_BILL_REJECT', [
                'user'         => $user->fullname,
                'amount'       => showAmount($bill->amount, currencyFormat: false),
                'charge'       => showAmount($bill->charge, currencyFormat: false),
                'utility'      => $company->name,
                'trx'          => $bill->trx,
                'reason'       => $reason,
                'time'         => now(),
                'post_balance' => showAmount($user->post_balance, currencyFormat: false),
            ]);
            $bill->status = Status::UTILITY_BILL_REFUNDED;
            $bill->save();
        }

        if ($transaction['status'] == 'SUCCESSFUL') {

            notify($user, 'UTILITY_BILL_APPROVE', [
                'user'         => $user->fullname,
                'amount'       => showAmount($bill->amount, currencyFormat: false),
                'charge'       => showAmount($bill->charge, currencyFormat: false),
                'utility'      => $company->name,
                'trx'          => $bill->trx,
                'time'         => now(),
                'post_balance' => showAmount($user->balance, currencyFormat: false),
            ]);

            $bill->status = Status::UTILITY_BILL_SUCCESSFUL;
            $bill->token = @$transaction['billDetails']['pinDetails']['token'];
            $bill->save();
        }
    }

    public function getBillers($country)
    {
        $headers    = $this->getHeaders();
        $allBillers = [];
        $page       = 1;
        $meta       = [];

        while (0 == 0) {
            $url             = $this->baseURL . "/billers?page=$page&size=100&countryISOCode=" . $country->iso_name;
            $response        = CurlRequest::curlContent($url, $headers);
            $responseContent = $this->validateHttpResponse($response);

            if (!isset($responseContent['content']) || empty($responseContent['content'])) break;

            array_push($allBillers, ...$responseContent['content']);

            if ($responseContent['totalPages'] == $page) break;
            $page++;
        };


        $meta['content']       = $allBillers;
        $meta['page']          = 0;
        $meta['size']          = count($allBillers);
        $meta['totalElements'] = count($allBillers);
        $meta['totalPages']    = 1;

        return json_decode(json_encode($meta));
    }
    public function getCountries()
    {
        $headers         = $this->getHeaders();
        $url             = $this->baseURL . "/countries";
        $response        = CurlRequest::curlContent($url, $headers);
        $responseContent = $this->validateHttpResponse($response);

        return $responseContent;;
    }

    public function importBillerCategory()
    {

        $headers      = $this->getHeaders();
        $totalImport  = 0;
        $page         = 1;
        $categoryData = [];

        while (0 == 0) {

            $url             = $this->baseURL . "/billers?page=$page";
            $response        = CurlRequest::curlContent($url, $headers);
            $responseContent = $this->validateHttpResponse($response);
            $billers         = collect($responseContent['content']);

            foreach ($billers->groupBy('type') as $type => $groupedBillers) {
                if (!BillCategory::where('name', $type)->exists()) {
                    $categoryData[] = [
                        'name'       => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $totalImport++;
                }
            }

            if ($responseContent['totalPages'] == $page) break;

            $page++;
        }


        BillCategory::insert($categoryData);

        return count($categoryData);
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

        if (isset($responseData['errorCode'])) {
            if ($responseData['errorCode'] == 'TOKEN_EXPIRED') {
                $this->createNewToken();
                throw new Exception("Token expired. New token is created. Please try again.");
            } else {
                throw new Exception(isset($responseData['message']) ? $responseData['message'] : 'Something went wrong');
            }
        }

        return $responseData;
    }
}
