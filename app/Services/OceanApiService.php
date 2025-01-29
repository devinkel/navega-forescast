<?php

namespace App\Services;

class OceanApiService extends AbstractApiService
{
    public function getForecast()
    {
        $params = [
            "latitude" => -26.8989,
            "longitude" => -48.6542,
            "daily" => ["wave_height_max", "wave_direction_dominant", "wave_period_max", "wind_wave_height_max", "wind_wave_direction_dominant", "wind_wave_period_max", "swell_wave_height_max", "swell_wave_direction_dominant", "swell_wave_period_max"],
            "timezone" => "America/Sao_Paulo"
        ];
        return $this->get($params);
    }
}
