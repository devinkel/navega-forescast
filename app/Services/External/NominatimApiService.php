<?php

namespace App\Services\External;

use GuzzleHttp\Client;

class NominatimApiService {

    /**
     * Get the coordinates for the given city and state.
     *
     * @param string $city The city.
     * @param string $state The state.
     *
     * @return array The coordinates.
     */
    public function getCoordinates(string $city, string $state): array
    {
        try {
            $client = new Client();
            $response = $client->get('https://nominatim.openstreetmap.org/search?format=json&city=' . $city . '&state=' . $state, [
                'headers' => [
                    'User-Agent' => 'ArtisanSurferForecast/1.0'
                ],
            ]);

            $dataArray =json_decode($response->getBody()->getContents(), true);

            $dataResponse = [
                'latitude' => isset($dataArray[0]) ? $dataArray[0]['lat'] : '',
                'longitude' => isset($dataArray[0]) ? $dataArray[0]['lon'] : ''
            ];

            return $dataResponse;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
