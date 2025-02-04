<?php

namespace App\Services\External;

class OceanApiService extends AbstractApiService
{

    /**
     * Faz uma solicita o para a API de previsão de ondas e retorna os dados formatados.
     * 
     * @return array Retorna um array com as previsões de ondas formatadas hora.
     */
    public function generateForecast(): array {
        $forecast = $this->getForecast();
        $normalize_forecast = $this->normalizeForecast($forecast);

        return $normalize_forecast;
    }


    /**
     * Faz uma solicita o para a API de previsão de ondas e retorna os dados em forma de array.
     * 
     * @return array Retorna um array com as previsões de ondas em forma de array.
     */
    protected function getForecast(): array
    {
        $params = [
            "latitude" => -26.8989,
            "longitude" => -48.6542,
            "hourly" => ["wave_height", "wave_direction", "wave_period", "swell_wave_height", "swell_wave_direction", "swell_wave_period"],
            "timezone" => "America/Sao_Paulo",
            "forecast_days" => 2
        ];

        $forecast = $this->get($params);

        return $forecast;
    }

    /**
     * Recebe um array com as previsões de ondas e as organiza por data e hora.
     * 
     * @param array $forecast Um array com as previsões de ondas.
     * 
     * @return array Um array com as previsões de ondas agrupadas por data e hora.
     * 
     * Exemplo de retorno:
     * [
     *     '2025-01-30' => [
     *         '00:00' => [...],
     *         '01:00' => [...],
     *         ...
     *     ],
     *     '2025-01-31' => [
     *         '00:00' => [...],
     *         '01:00' => [...],
     *         ...
     *     ]
     * ]
     */
    protected function normalizeForecast(array $forecast): array
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
