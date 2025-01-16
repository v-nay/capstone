<?php

namespace App\Services\System;

use App\Services\Service;
use GuzzleHttp\Client;

class GoogleService extends Service
{
    protected $client;

    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.google.api_key');
    }

    public function searchPlaces($query, $location, $radius = 5000)
    {
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

        $response = $this->client->get($url, [
            'query' => [
                'query' => $query,
                'location' => $location,
                'radius' => $radius,
                'key' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getSuburbLocation($suburb)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';

        $response = $this->client->get($url, [
            'query' => [
                'address' => $suburb,
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        // Check if results exist
        if (! empty($data['results'][0]['geometry']['location'])) {
            $location = $data['results'][0]['geometry']['location'];

            return "{$location['lat']},{$location['lng']}";
        }

        throw new \Exception("Location for suburb '$suburb' not found");
    }

    public function searchPlacesWithPagination($query, $location, $radius = 5000)
    {
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        $allResults = [];
        $pageToken = null;

        do {
            $queryParams = [
                'query' => $query,
                'location' => $location,
                'radius' => $radius,
                'key' => $this->apiKey,
            ];

            if ($pageToken) {
                $queryParams['pagetoken'] = $pageToken;
                sleep(2);
            }

            $response = $this->client->get($url, ['query' => $queryParams]);
            $data = json_decode($response->getBody(), true);

            // Merge results
            if (isset($data['results'])) {
                $allResults = array_merge($allResults, $data['results']);
            }

            $pageToken = $data['next_page_token'] ?? null;
        } while ($pageToken);

        return $allResults;
    }
}
