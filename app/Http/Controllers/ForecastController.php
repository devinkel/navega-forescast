<?php

namespace App\Http\Controllers;

use App\Services\External\OceanApiService;
use App\Services\Internal\ForecastService;

class ForecastController extends Controller
{

    public function index(ForecastService $forecastService)
    {
        return $forecastService->getAll();
    }

    public function getLiveForecast(ForecastService $forecastService)
    {

        $oceanApiService = new OceanApiService(config('ocean_client_api'));

        $existentDayForecast = $forecastService->getForecastByCreatedAt(date('Y-m-d'));
        
        if ($existentDayForecast->getData()->data) {
            return $existentDayForecast;
        };

        $forecast = $oceanApiService->generateForecast();
        $data = [
            'beach_id' => 1,
            'forecast' => $forecast
        ];

        $forecastService->create($data);

        return response()->json([
            'error' => false,
            'data' => [
                'forecast' => $forecast
            ]
        ], 200);
    }
}
