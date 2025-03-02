<?php

namespace App\Services\External;

class OceanApiService extends AbstractApiService
{

    /**
     * Generates a forecast for a given location based on latitude and longitude.
     *
     * This method retrieves the forecast data using the Ocean API and returns it
     * as an array. The default coordinates are for a specific location.
     *
     * @param string $latitude The latitude of the location for which to generate the forecast. Defaults to '-26.8989'.
     * @param string $longitude The longitude of the location for which to generate the forecast. Defaults to '-48.6542'.
     *
     * @return array The forecast data for the specified location.
     */

    public function generateForecast(string $latitude = '-26.8989', string $longitude = '-48.6542'): array
    {
        $forecast = $this->getForecast($latitude, $longitude);

        return $forecast;
    }
    
    /**
     * Retrieves the forecast data from the Ocean API for the specified location and normalizes it.
     * 
     * @param string $latitude The latitude of the location for which to retrieve the forecast. Defaults to '-26.8989'.
     * @param string $longitude The longitude of the location for which to retrieve the forecast. Defaults to '-48.6542'.
     * 
     * @return array The normalized forecast data, or an array with an 'error' key if the forecast could not be retrieved.
     */
    protected function getForecast(string $latitude = '-26.8989', string $longitude = '-48.6542'): array
    {
        $params = [
            "latitude" => $latitude,
            "longitude" => $longitude,
            "hourly" => ["wave_height", "wave_direction", "wave_period", "swell_wave_height", "swell_wave_direction", "swell_wave_period", "wind_wave_direction"],
            "timezone" => "America/Sao_Paulo",
            "forecast_days" => 1,
            'temporal_resolution' => 'hourly_3', 
        ];

        $forecast = $this->get($params);
        
        if (empty($forecast)) {
            return ['error' => 'Forecast not found'];
        }

        return  $this->normalizeForecast($forecast);
    }

    /**
     * Normalize the forecast returned by Ocean API.
     * 
     * The normalization is done by grouping the forecast by date and time.
     * 
     * @param array $forecast The forecast returned by Ocean API.
     * 
     * @return array The normalized forecast.
     */
    protected function normalizeForecast(array $forecast): array
    {

        $hourly = isset($forecast['hourly']) ? $forecast['hourly'] : [];
        $hourly_units = isset($forecast['hourly_units']) ? $forecast['hourly_units'] : [];

        // group by date and time
        $grouped_by_date_and_time = [];

        if (count($hourly) >= 1 && isset($hourly['time'])) {
            foreach ($hourly['time'] as $index => $time) {
                $hour = substr($time, 11, 5); // "00:00", "01:00", etc.

                
                $grouped_by_date_and_time[$hour] = [
                    'wave_height' => $hourly['wave_height'][$index] . $hourly_units['wave_height'],
                    'wave_direction' => $this->formatValue($hourly['wave_direction'][$index], 'wave_direction'),
                    'wave_period' => $hourly['wave_period'][$index] . $hourly_units['wave_period'],
                    'swell_wave_height' => $hourly['swell_wave_height'][$index] . $hourly_units['swell_wave_height'],
                    'swell_wave_direction' => $this->formatValue($hourly['swell_wave_direction'][$index], 'swell_wave_direction'),
                    'swell_wave_period' => $hourly['swell_wave_period'][$index] . $hourly_units['swell_wave_period'],
                    'wind_wave_direction' => $this->formatValue($hourly['wind_wave_direction'][$index], 'wind_wave_direction'),
                ];
            }
        }

        return $grouped_by_date_and_time;
    }

    /**
     * Converts a given angle in degrees to a compass direction.
     *
     * This method normalizes the angle to fall within the range of 0 to 360 degrees
     * and maps it to one of the 16 compass directions (e.g., N, NNE, NE, etc.).
     *
     * @param float $angle The angle in degrees to be converted to a compass direction.
     * @return string The compass direction corresponding to the given angle.
     */

    protected function getWindDirection($angle) : string {
        $directions = [
            "N", "NNE", "NE", "ENE",
            "E", "ESE", "SE", "SSE",
            "S", "SSW", "SW", "WSW",
            "W", "WNW", "NW", "NNW"
        ];
    
        // Normalize the angle to ensure it's between 0 and 360
        $angle = ($angle + 360) % 360;
    
        // Calculate the index corresponding to the direction
        $index = round($angle / 22.5) % 16;
    
        return $directions[$index];
    }
    
    /**
     * Format a given value according to the unit type.
     *
     * If the key contains the word "direction", the value is assumed to be an angle in
     * degrees and is converted to a compass direction (e.g., N, NNE, NE, etc.).
     *
     * Otherwise, the value is returned unchanged.
     *
     * @param mixed $value The value to be formatted
     * @param string $key The key corresponding to the value, which determines the unit type
     * @return mixed The formatted value
     */
    protected function formatValue($value, $key) {
        // If it's a wind direction
        if (strpos($key, 'direction') !== false) {
            // Remove the degree symbol if it exists and convert to a number
            $angle = floatval($value);
            return $this->getWindDirection($angle);
        }
    
        return $value;
    }
}
