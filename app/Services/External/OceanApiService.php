<?php

namespace App\Services\External;

class OceanApiService extends AbstractApiService
{

  
    /**
     * Get the forecast for the day from Ocean API and normalize it.
     *
     * @param string $latitude
     * @param string $longitude
     * @return array
     */
    public function generateForecast(string $latitude = '-26.8989', string $longitude = '-48.6542'): array
    {
        $forecast = $this->getForecast($latitude, $longitude);
        $normalize_forecast = $this->normalizeForecast($forecast);

        return $normalize_forecast;
    }


    /**
     * Get the forecast for the day from Ocean API.
     *
     * @param string $latitude
     * @param string $longitude
     * @return array
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

        return $forecast;
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
                    'wave_direction' => $hourly['wave_direction'][$index],
                    'wave_period' => $hourly['wave_period'][$index] . $hourly_units['wave_period'],
                    'swell_wave_height' => $hourly['swell_wave_height'][$index] . $hourly_units['swell_wave_height'],
                    'swell_wave_direction' => $hourly['swell_wave_direction'][$index],
                    'swell_wave_period' => $hourly['swell_wave_period'][$index] . $hourly_units['swell_wave_period'],
                    'wind_wave_direction' => $hourly['wind_wave_direction'][$index]
                ];
            }
        }

        return $grouped_by_date_and_time;
    }
}
