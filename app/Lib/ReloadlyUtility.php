<?php

namespace App\Lib;

use App\Models\ApiProvider;
use Illuminate\Validation\ValidationException;

class ReloadlyUtility
{
    private $baseURL         = 'https://utilities.reloadly.com';
    private $sandboxURL      = 'https://utilities-sandbox.reloadly.com';
    private $baseAudience    = 'https://utilities.reloadly.com';
    private $sandboxAudience = 'https://utilities-sandbox.reloadly.com';
    private $accessToken;
    private $accessTokenType;
    private $url;
    private $audience;

    public function __construct()
    {
        $this->setAccessToken();
    }

    private function setAccessToken()
    {
        $apiConfig = ApiProvider::where('provider', 'reloadly_utility')->first();

        if (!$apiConfig) {
            throw ValidationException::withMessages(['error' => 'Utility API configuration not found.']);
        }

        $this->url      = $apiConfig->test_mode ? $this->sandboxURL : $this->baseURL;
        $this->audience = $apiConfig->test_mode ? $this->sandboxAudience : $this->baseAudience;

        $credentials       = $apiConfig->credentials;
        $this->accessToken = $apiConfig->access_token;

        if (!$this->accessToken || @$apiConfig->token_expired_on < now()) {
            $authURL = 'https://auth.reloadly.com/oauth/token';

            $data = json_encode([
                'client_id'     => @$credentials->client_id,
                'client_secret' => @$credentials->client_secret,
                'grant_type'    => 'client_credentials',
                'audience'      => $this->audience
            ]);

            $headers = ['Content-Type: application/json'];

            $response = CurlRequest::curlPostContent($authURL, $data, $headers);
            $response = json_decode($response);

            if (!@$response->access_token) {
                throw ValidationException::withMessages(['error' => 'Failed to get access token from Reloadly.']);
            }

            $apiConfig->token_type       = $response->token_type;
            $apiConfig->access_token     = $response->access_token;
            $apiConfig->token_expired_on = now()->addSeconds($response->expires_in);
            $apiConfig->save();

            $this->accessToken     = $response->access_token;
            $this->accessTokenType = $response->token_type;
        } else {
            $this->accessTokenType = $apiConfig->token_type;
        }
    }

    private function getHeaders()
    {
        return [
            "Authorization: {$this->accessTokenType} {$this->accessToken}",
            "Accept: application/com.reloadly.utilities-v1+json"
        ];
    }

    public function getBillers()
    {
        $url = $this->url . '/billers';
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function getBillerById($billerId)
    {
        $url = $this->url . '/billers/' . $billerId;
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function getTransactions()
    {
        $url = $this->url . '/transactions';
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function getTransactionById($transactionId)
    {
        $url = $this->url . '/transactions/' . $transactionId;
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function getBalance()
    {
        $url = $this->url . '/accounts/balance';
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function payBill($billerId, $subscriberAccountNumber, $amount, $referenceId, $useLocalAmount = false)
    {
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $data = json_encode([
            'subscriberAccountNumber' => $subscriberAccountNumber,
            'amount'                  => $amount,
            'billerId'                => $billerId,
            'useLocalAmount'          => $useLocalAmount,
            'referenceId'             => $referenceId
        ]);

        $url = $this->url . '/pay';

        try {
            $response = CurlRequest::curlPostContent($url, $data, $headers);
            return json_decode($response);
        } catch (\Exception $e) {
            return (object)[
                'status'  => 'ERROR',
                'message' => $e->getMessage()
            ];
        }
    }
}
