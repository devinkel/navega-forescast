<?php

namespace App\Services\Internal;

use App\Models\Forecasts;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class ForecastService
{

    public function create(array $forecastData)
    {
        $validator = Validator::make($forecastData, [
            'beach_id' => 'required|exists:beaches,id',
            'forecast' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 400);
        }

        try {
            $forecast = Forecasts::create($forecastData);
            return response()->json([
                'error' => false,
                'message' => 'Previsão criada com sucesso',
                'data' => $forecast
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAll()
    {
        $forecasts = Forecasts::all();

        return response()->json([
            'error' => false,
            'data' => $forecasts
        ], 200);
    }
}
