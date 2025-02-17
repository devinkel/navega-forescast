<?php

namespace App\Services\Internal;

use App\Models\Forecasts;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse as JsonResponse;

class ForecastService
{

    /**
     * Create a new forecast.
     *
     * @param array $forecastData
     *   The data for the forecast. Must contain 'beach_id' and 'forecast' keys.
     *   'beach_id' must be the ID of an existing beach.
     *   'forecast' must be an array of forecast data for the given beach.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A JSON response with a 200 status code and the created forecast data
     *   if the operation is successful, or a 400 or 500 status code and an
     *   error message if the operation fails.
     */
    
    public function create(array $forecastData) : JsonResponse
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

    /**
     * Retrieve all forecasts.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A JSON response containing all forecasts with a 200 status code.
     *   The 'error' key in the response is set to false, and the 'data' key
     *   contains the list of all forecasts.
     */

    public function getAll(): JsonResponse
    {
        $forecasts = Forecasts::all();
        return response()->json([
            'error' => false,
            'data' => $forecasts
        ], 200);
    }

    /**
     * Retrieve a forecast by its ID.
     *
     * @param int $id
     *   The ID of the forecast to retrieve.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A JSON response with a 200 status code and the forecast data if found.
     *   If the forecast is not found, returns a 404 status code with an error message.
     */

    public function getById(int $id): JsonResponse
    {
        $forecast = Forecasts::find($id);

        if (!$forecast) {
            return response()->json([
                'error' => true,
                'message' => 'Forecast not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'error' => false,
            'data' => $forecast
        ], 200);
    }

    /**
     * Delete a forecast by its ID.
     *
     * @param int $id
     *   The ID of the forecast to delete.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A JSON response with a 200 status code and a success message if the forecast is deleted.
     *   If the forecast is not found, returns a 404 status code with an error message.
     */

    public function delete(int $id): JsonResponse
    {
        $forecast = Forecasts::find($id);

        if (!$forecast) {
            return response()->json([
                'error' => true,
                'message' => 'Forecast not found.'
            ], 404);
        }

        return response()->json([
            'error' => false,
            'message' => 'Forecast deleted successfully.'
        ], 200);
    }

    
    /**
     * Retrieve a forecast by the creation date.
     *
     * @param string $createdAt
     *   The date to filter forecasts by creation date.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A JSON response with a 200 status code and the forecast data for the given date.
     *   The 'error' key in the response is set to false, and the 'data' key contains the forecast details.
     *   If no forecast is found for the given date, 'data' will contain null.
     */

    public function getForecastByCreatedAt($createdAt): JsonResponse
    {
        $forecast = Forecasts::whereDate('created_at', $createdAt)->first();

        return response()->json([
            'error' => false,
            'data' => $forecast
        ], 200);
    }
}

