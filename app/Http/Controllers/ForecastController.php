<?php

namespace App\Http\Controllers;

use App\Services\External\OceanApiService;
use App\Services\Internal\ForecastService;

class ForecastController extends Controller
{

    /**
     * @api {get} /forecast Get all forecasts
     * @apiName GetAllForecast
     * @apiGroup Forecast
     *
     * @apiSuccess {Boolean} error Error flag
     * @apiSuccess {Object[]} data Forecast data
     * @apiSuccess {Integer} data.id Forecast ID
     * @apiSuccess {Object[]} data.forecast Forecast grouped by date and time
     *
     */
    public function index(ForecastService $forecastService)
    {
        return $forecastService->getAll();
    }

    /**
     * @api {get} /forecast/live Get live forecast
     * @apiName GetLiveForecast
     * @apiGroup Forecast
     *
     * @apiSuccess {Boolean} error Error flag
     * @apiSuccess {Object} data Forecast data
     * @apiSuccess {Object[]} data.forecast Forecast grouped by date and time

     */
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
