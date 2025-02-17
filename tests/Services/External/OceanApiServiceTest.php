<?php

namespace Tests\Services\External;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\External\OceanApiService;

/**
 * @testdox --OceanApiService
 *
 */

class OceanApiServiceTest extends TestCase
{
    /**
     * 
     * @testdox Sucesso ao gerar previsão
     *
     */

    public function testForecastReturnSuccessDeleteExistentForecast()
    {
        $configs = [
            'url' => env('CLIENT_FORECAST_API'),
            'timeout' => env('CLIENT_FORECAST_TIMEOUT', 30),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ];
        
        $newForecast = new OceanApiService($configs);
        $forecast = $newForecast->generateForecast();

    }
}
