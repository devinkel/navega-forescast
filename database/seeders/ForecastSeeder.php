<?php

namespace Database\Seeders;

use App\Models\Beach;
use App\Models\Forecasts;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ForecastSeeder extends Seeder
{
    public function run()
    {
        // Buscar algumas praias existentes para associar as previsões
        $beaches = Beach::all(); // Ou Beach::limit(10)->get(); caso queira limitar o número de praias

        foreach ($beaches as $beach) {
            // Gerar o conjunto de previsões para este modelo de praia
            $forecastData = $this->generateForecastData();

            // Criar a previsão no banco de dados
            Forecasts::create([
                'beach_id' => $beach->id,  // Associando a previsão com a praia
                'forecast' => $forecastData, // Salvando os dados como JSON
            ]);
        }
    }

    /**
     * Gerar os dados de previsão para um conjunto de dias e horas
     *
     * @return array
     */
    private function generateForecastData()
    {
        // Gerar previsões para 3 dias
        $forecastData = [];

        foreach (range(0, 2) as $i) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            $forecastData[$date] = [];

            // Gerar previsões para cada hora do dia
            foreach (range(0, 23) as $hour) {
                $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';

                $forecastData[$date][$time] = [
                    'swell_wave_height' => rand(30, 200) / 100 . 'm',  // Gerando um valor aleatório para swell_wave_height entre 0.3m e 2m
                    'wave_height' => rand(50, 250) / 100 . 'm',  // Gerando um valor aleatório para wave_height entre 0.5m e 2.5m
                    'wave_direction' => rand(80, 180),  // Direção das ondas entre 80° e 180°
                    'wave_period' => rand(50, 100) / 10 . 's',  // Período de ondas entre 5.0s e 10.0s
                    'swell_wave_direction' => rand(80, 120),  // Direção do swell entre 80° e 120°
                    'swell_wave_period' => rand(60, 100) / 10 . 's',  // Período do swell entre 6.0s e 10.0s
                ];
            }
        }

        return $forecastData;
    }
}
