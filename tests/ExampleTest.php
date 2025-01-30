<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Services\OceanApiService;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_ocean_api()
    {
        $config = config('ocean_client_api');
        $oceanApi = new OceanApiService($config);
        dd($oceanApi->getForecast());
    }
}
