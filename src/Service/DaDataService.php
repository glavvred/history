<?php

namespace App\Service;

use GuzzleHttp\Client;

class DaDataService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey = '')
    {
        $this->client = new Client(['base_uri' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/']);
        $this->apiKey = $apiKey;
    }

    public function suggestAddress(string $query): array
    {
        $response = $this->client->post('address', [
            'headers' => [
                'Authorization' => 'Token ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => ['query' => $query],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['suggestions'] ?? [];
    }
}
