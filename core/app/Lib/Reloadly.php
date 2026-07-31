<?php

namespace App\Lib;

use App\Models\ApiProvider;
use Exception;

class Reloadly
{
    private $baseURL        = 'https://topups.reloadly.com';
    public  $useLocalAmount = false;
    public  $operatorId;
    public  $apiConfig;

    public function __construct()
    {
        $apiConfig = ApiProvider::where('provider', 'reloadly_api')->first();

        if (!$apiConfig) {
            throw new Exception("Reloadly API configuration is missing. Please check your API provider settings.");
        }

        $this->apiConfig = $apiConfig;
        $environment     = collect($this->apiConfig->config)->where('name', 'environment')->first();

        if (!$environment) {
            throw new Exception('Utility API configuration not found.');
        }
        if ($environment->value == 'production') {
            $this->baseURL = "https://topups.reloadly.com";
        } else {
            $this->baseURL = "https://topups-sandbox.reloadly.com";
        }
    }

    public function getCountries()
    {
        $url          = $this->baseURL . '/countries';
        $response     = CurlRequest::curlContent($url);
        $responseData = $this->validateHttpResponse($response);
        return $responseData;
    }

    public function getOperators()
    {
        $url = $this->baseURL . '/operators';
        $response = CurlRequest::curlContent($url, $this->getHeaders());
        return json_decode($response);
    }

    public function getOperatorsByISO($iso)
    {
        $url     = $this->baseURL . '/operators/countries/' . $iso;
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function getOperatorByISOAndNumber($iso, $number)
    {
        $iso     = strtoupper($iso);
        $number  = urlencode($number);
        $url     = $this->baseURL . '/operators/auto-detect/phone/' . $number . '/countries/' . $iso;
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function topUp($amount, $recipient)
    {
        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $data = [
            "operatorId"     => $this->operatorId,
            "amount"         => $amount,
            "useLocalAmount" => false,
            "recipientPhone" => $recipient
        ];

        $data         = json_encode($data);
        $topUpURL     = $this->baseURL . '/topups';
        $response     = CurlRequest::curlPostContent($topUpURL, $data, $headers);
        $responseData = $this->validateHttpResponse($response);

        if (@$responseData['status'] == 'SUCCESSFUL') {
            return [
                'status'            => true,
                'cost'              => $responseData['balanceInfo']['cost'],
                'currencyCode'      => $responseData['balanceInfo']['currencyCode'],
                'custom_identifier' => $responseData['customIdentifier']
            ];
        } else {
            return [
                'status'  => false,
                'message' => @$responseData['message']
            ];
        }
    }
    public function updateOperator($operatorId)
    {

        $headers = array_merge($this->getHeaders(), [
            'Content-Type: application/json'
        ]);

        $url          = $this->baseURL . "/operators/$operatorId";
        $response     = CurlRequest::curlContent($url, $headers);
        $responseData = $this->validateHttpResponse($response);

        return $responseData;
    }

    public function getHeaders()
    {
        $accessToken    = collect($this->apiConfig->access_token)->where('name', 'airtime_token')->first();
        $tokenValue     = $accessToken->value;
        $tokenExpiredAt = $accessToken->expired_at ? now()->parse($accessToken->expired_at) : now();



        if (!$tokenValue || !$tokenExpiredAt ||  $tokenExpiredAt < now() || is_null($tokenExpiredAt)) {
            $this->createNewToken();
        }

        $apiConfig       = $this->apiConfig;
        $accessToken     = $apiConfig->access_token->airtime_token->value;
        $accessTokenType = $apiConfig->access_token->airtime_token->token_type;

        return [
            "Authorization: $accessTokenType $accessToken",
            "Accept: application/com.reloadly.topups-v1+json"
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
        $accessToken->airtime_token->token_type = $response->token_type;
        $accessToken->airtime_token->value      = $response->access_token;
        $accessToken->airtime_token->expired_at = now()->addSeconds($response->expires_in);
        $apiConfig->access_token                = $accessToken;
        $apiConfig->save();

        $this->apiConfig = $apiConfig;
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
                $trace = debug_backtrace();
                $immediateCaller     = $trace[1]['function'];
                $immediateCallerArgs = $trace[1]['args'];
                $this->$immediateCaller(...$immediateCallerArgs);
            } else {
                throw new Exception(isset($responseData['message']) ? $responseData['message'] : 'Something went wrong');
            }
        }

        return $responseData;
    }
}
