<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Services\External\OceanApiService;

class OceanApiServiceTest extends TestCase
{

    /**
     * @testdox --OceanApiService : Testa a estrutura normalizada dos dados retornados da API
    
     * @group howto
     */

    public function testForecastFormat()
    {
        $config = config('ocean_client_api');
        $oceanApi = new OceanApiService($config);
        $response = $oceanApi->getForecast(); //tipo array
        // dd($response);

                // Verifique se existem datas (como "2025-02-01", "2025-02-02", etc.)
        foreach ($response as $date => $times) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date, "Data inválida: $date");

            // Verifique se existem as horas corretas (00:00, 01:00, ..., 23:00)
            foreach ($times as $time => $details) {
                $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $time, "Hora inválida: $time");

                // Verifique se a estrutura para cada horário tem as chaves esperadas
                $this->assertArrayHasKey('swell_wave_height', $details);
                $this->assertArrayHasKey('wave_height', $details);
                $this->assertArrayHasKey('wave_direction', $details);
                $this->assertArrayHasKey('wave_period', $details);
                $this->assertArrayHasKey('swell_wave_direction', $details);
                $this->assertArrayHasKey('swell_wave_period', $details);

                // Verifique se os valores estão em formato de string (não vazio)
                $this->assertIsString($details['swell_wave_height']);
                $this->assertIsString($details['wave_height']);
                $this->assertIsString($details['wave_direction']);
                $this->assertIsString($details['wave_period']);
                $this->assertIsString($details['swell_wave_direction']);
                $this->assertIsString($details['swell_wave_period']);

                // Se você quiser testar se os valores são numéricos seguidos de "m" ou "s", pode adicionar algo como:
                $this->assertMatchesRegularExpression('/^\d+(\.\d+)?[m|s]$/', $details['swell_wave_height']);
                $this->assertMatchesRegularExpression('/^\d+(\.\d+)?[m|s]$/', $details['wave_height']);
                $this->assertMatchesRegularExpression('/^\d+(\.\d+)?\s?°$/', $details['wave_direction']);
                // $this->assertMatchesRegularExpression('/^\d+(\.\d+)?[°]$/', $details['wave_direction']);
                $this->assertMatchesRegularExpression('/^\d+(\.\d+)?s$/', $details['wave_period']);
            }
        }
    }
}
