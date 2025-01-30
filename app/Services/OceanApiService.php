<?php

namespace App\Services;

class OceanApiService extends AbstractApiService
{
    public function getForecast()
    {
        $params = [
            "latitude" => -26.8989,
            "longitude" => -48.6542,
            "hourly"=> ["wave_height", "wave_direction", "wave_period", "swell_wave_height", "swell_wave_direction", "swell_wave_period", "swell_wave_peak_period", "ocean_current_velocity", "ocean_current_direction"],
            "timezone" => "America/Sao_Paulo",
            "forecast_days" => 1
        ];
        return $this->get($params);
    }
}
