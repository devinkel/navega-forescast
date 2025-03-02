<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\External\NominatimApiService;
use App\Services\External\OceanApiService;

use function Laravel\Prompts\suggest;

class ForecastRunner extends Command
{
    // O nome do comando que será chamado no Artisan
    protected $signature = 'forecast:run';

    // A descrição do comando
    protected $description = 'Busca as previsões de ondas';


    public function handle()
    {
        $location = $this->suggestionLocation();

        $this->info('Getting coordinates...');
        $coordinates = (new NominatimApiService())->getCoordinates($location['city'], $location['state']);

        $this->info('Coordinates found: ' . $coordinates['latitude'] . ', ' . $coordinates['longitude']);
        if (empty($coordinates['latitude']) || empty($coordinates['longitude'])) {
            $this->error('Sem forecast para essa cidade...');
            return;
        }

        $this->info('Buscando forecast...');
        $oceanApiService = new OceanApiService(config('ocean_client_api'));
        $forecast = $oceanApiService->generateForecast($coordinates['latitude'], $coordinates['longitude']);
        $forecastTime = array_keys($forecast);

        $rows = $this->createRows($forecastTime, $forecast);
        $this->generateTable($rows);
    }

    protected function suggestionLocation()
    {
        $coastalStatesAndCities = config('coastal_cities_in_brazil');

        $states = array_keys($coastalStatesAndCities);
        $selectedState = suggest(
            label: 'Selecione o estado',
            options: $states,
            placeholder: 'Selecione o estado',
            hint: 'Use as setas do teclado para navegar',
            required: true
        );

        $cities = $coastalStatesAndCities[$selectedState];
        $selectedCity = suggest(
            label: 'Selecione a cidade',
            options: $cities,
            placeholder: 'Selecione a cidade',
            hint: 'Use as setas do teclado para navegar',
            required: true
        );

        $formatedState = str_replace(' ', '-', $selectedState);
        $formatedCity = str_replace(' ', '-', $selectedCity);

        return [
            'state' => $formatedState,
            'city' => $formatedCity
        ];
    }

    protected function createRows(array $forecastTime, array $forecast): array
    {
        $rows = [];
        foreach ($forecastTime as $time) {
            if (isset($forecast[$time])) {
                $forecastData = $forecast[$time];

                $row = [
                    $time,
                    $forecastData['wave_height'] ?? 'N/A',
                    $forecastData['wave_direction'] ?? 'N/A',
                    $forecastData['wave_period'] ?? 'N/A',
                    $forecastData['swell_wave_height'] ?? 'N/A',
                    $forecastData['swell_wave_direction'] ?? 'N/A',
                    $forecastData['swell_wave_period'] ?? 'N/A',
                    $forecastData['wind_wave_direction'] ?? 'N/A',
                ];

                $rows[] = $row;
            } else {
                $rows[] = [$time, 'Dados não encontrados'];
            }
        }

        return $rows;
    }

    protected function generateTable(array $rows)
    {
        $headers = [
            'Time',
            'Wave Height',
            'Wave Direction',
            'Wave Period',
            'Swell Height',
            'Swell Direction',
            'Swell Period',
            'Wind Direction'
        ];

        $this->info('Generating table for forecast...');
        $this->table($headers, $rows);
    }
}
