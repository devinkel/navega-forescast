<?php

namespace Tests\Services\External;

use PHPUnit\Framework\TestCase;

use App\Services\External\NominatimApiService;

use GuzzleHttp\Client;

/**
 * @testdox --ServiceDataIBGE
 *
 */

class NominatimApiTest extends TestCase
{
    /**
     * 
     * @testdox Test API nominatim is working
     *
     */

    public function testNominatimApi()
    {


        $client = new Client();

        $response = $client->get('https://nominatim.openstreetmap.org/search?format=json&city=Navegantes&state=Santa-Catarina', [
            'headers' => [
                'User-Agent' => 'ArtisanSurferForecast/1.0'
            ],
        ]);
    
        $statusCode = $response->getStatusCode();
        $this->assertEquals($statusCode, 200);
    }
}
