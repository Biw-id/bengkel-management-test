<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Exception;

class MainDealerApiService
{
    protected Client $client;

    public function __construct()
    {
        // Set timeout to 10 seconds as per the External-service conventions
        $this->client = new Client([
            'timeout'  => 10,
            'verify'   => false, // Adjust based on SSL verification needs
        ]);
    }

    /**
     * Submit completed Work Order to Main Dealer
     */
    public function submitTransaction(array $payload): void
    {
        $apiKey = env('DGI_API_KEY');
        $woId = $payload['noWorkOrder'] ?? 'UNKNOWN';

        if (empty($apiKey)) {
            Log::error('Main Dealer API Submit Failed: DGI_API_KEY is not set in .env');
            throw new Exception("API_CONNECTION_FAILED");
        }

        $url = 'https://dgitest.menara-agung.com/dgi-api/v1.5/inv2/ujian/add';

        try {
            Log::info('SUBMIT_DGI_API_PAYLOAD', ['wo_id' => $woId, 'payload' => $payload]);

            $response = $this->client->post($url, [
                'headers' => [
                    'DGI-API-Key' => $apiKey,
                    'Accept' => 'application/json'
                ],
                'json' => $payload
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('SUBMIT_DGI_API_RESPONSE', ['wo_id' => $woId, 'response' => $responseBody]);

            if ($statusCode >= 400 || !isset($responseBody['status']) || (int)$responseBody['status'] !== 1) {
                Log::error('Main Dealer API Submit Failed with status ' . $statusCode . ' and response: ' . json_encode($responseBody));
                throw new Exception("API_CONNECTION_FAILED");
            }
        } catch (GuzzleException $e) {
            Log::error('Main Dealer API Submit Request Failed: ' . $e->getMessage());
            throw new Exception("API_CONNECTION_FAILED");
        }
    }
}
