<?php

namespace App\Services;

class OceanApiService extends AbstractApiService
{
    public function getForecast()
    {
        $params = [
            "latitude" => -26.8989,
            "longitude" => -48.6542,
            "hourly" => ["wave_height", "wave_direction", "wave_period", "swell_wave_height", "swell_wave_direction", "swell_wave_period"],
            "timezone" => "America/Sao_Paulo",
            "forecast_days" => 2
        ];

        $forecast = $this->get($params);

        $normalize_forecast = $this->normalizeForecast($forecast);

        return $normalize_forecast;
    }

    public function normalizeForecast(array $forecast): array
    {

        $hourly = isset($forecast['hourly']) ? $forecast['hourly'] : [];
        $hourly_units = isset($forecast['hourly_units']) ? $forecast['hourly_units'] : [];

        // Vamos agrupar os dados por data e hora
        $grouped_by_date_and_time = [];

        if (count($hourly) >= 1 && isset($hourly['time'])) {
            foreach ($hourly['time'] as $index => $time) {
                // Extraímos a data (ano-mês-dia) e a hora (hora:minuto) da string de tempo
                $date = substr($time, 0, 10); // "2025-01-30"
                $hour = substr($time, 11, 5); // "00:00", "01:00", etc.

                // Se o dia ainda não existe no array, criamos ele
                if (!isset($grouped_by_date_and_time[$date])) {
                    $grouped_by_date_and_time[$date] = [];
                }

                // Adicionamos os dados da hora atual no dia e hora correspondentes
                $grouped_by_date_and_time[$date][$hour] = [
                    'swell_wave_height' => $hourly['swell_wave_height'][$index] . $hourly_units['swell_wave_height'],
                    'wave_height' => $hourly['wave_height'][$index] . $hourly_units['wave_height'],
                    'wave_direction' => $hourly['wave_direction'][$index] . $hourly_units['wave_direction'],
                    'wave_period' => $hourly['wave_period'][$index] . $hourly_units['wave_period'],
                    'swell_wave_direction' => $hourly['swell_wave_direction'][$index] . $hourly_units['swell_wave_direction'],
                    'swell_wave_period' => $hourly['swell_wave_period'][$index] . $hourly_units['swell_wave_period']
                ];
            }
        }

        return $grouped_by_date_and_time;
    }
}
