<?php

namespace Tests;

use App\Services\Internal\ForecastService;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * @testdox --ForecastService
 *
 */
class ForecastSeviceTest extends TestCase
{

    use DatabaseTransactions;
    /**
     * @testdox Falha ao criar forecast com beach_id inválido
     *
     */

    public function testForecastReturnErrorWithInvalidBeachId()
    {
        $invalidForecast =  [
            'beach_id' => 999, // ID não existe
            'forecast' => [
                '2025-02-01' => [
                    '00:00' => [
                        'swell_wave_height' => '0.48m',
                        'wave_height' => '0.7m',
                    ],
                ]
            ]
        ];

        $newForecast = new ForecastService();

        $response = $newForecast->create($invalidForecast);

        $this->assertEquals($response->getStatusCode(), 400);
        $this->assertEquals(json_decode($response->getContent(), true)['error'], true);
    }

    /**
     * @testdox Falha ao criar forecast sem beach_id
     *
     */

    public function testForecastReturnErrorWithoutBeachId()
    {
        $invalidForecast =  [
            'forecast' => [
                '2025-02-01' => [
                    '00:00' => [
                        'swell_wave_height' => '0.48m',
                        'wave_height' => '0.7m',
                    ],
                ]
            ]
        ];

        $newForecast = new ForecastService();

        $response = $newForecast->create($invalidForecast);

        $this->assertEquals($response->getStatusCode(), 400);
        $this->assertEquals(json_decode($response->getContent(), true)['message']['beach_id'], ['The beach id field is required.']);
    }

    /**
     * @testdox Sucesso ao criar um forecast
     *
     */

    public function testForecastSuccess()
    {
        $validForecast =  [
            'beach_id' => 1,
            'forecast' => [
                '2025-02-03' => [
                    '00:00' => [
                        'swell_wave_height' => '0.48m',
                        'wave_height' => '0.7m',
                    ],
                ]
            ]
        ];

        $newForecast = new ForecastService();

        $response = $newForecast->create($validForecast);
        $validResponseToTest = json_decode(json_encode($response->getData()->data), true);
        
        unset($validResponseToTest['updated_at']);
        unset($validResponseToTest['created_at']);
        unset($validResponseToTest['id']);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals(json_decode($response->getContent(), true)['error'], false);
        $this->assertEquals($validResponseToTest, $validForecast);
    }

    /**
     * @testdox Retorna todos os forecasts
     *
     */

    public function testForecastReturnAllForecasts()
    {
        $newForecast = new ForecastService();

        $forecasts = $newForecast->getAll();
        $formatedForescasts = json_decode($forecasts->getContent(), true);
        $this->assertEquals($formatedForescasts['error'], false);
        $this->assertNotEmpty($formatedForescasts['data']);
    }

    /**
     * @testdox Falha ao obter Forecast inexistente
     *
     */

    public function testForecastReturnErrorWithInexistentForecast()
    {
        $newForecast = new ForecastService();

        $forecasts = $newForecast->getById(9999); // ID não exista;
        $formatedForescast = json_decode($forecasts->getContent(), true);
        $this->assertEquals($formatedForescast['error'], true);
        $this->assertEquals($formatedForescast['message'], 'Forecast not found.');
        $this->assertEmpty($formatedForescast['data']);
    }

    /**
     * @testdox Sucesso ao obter Forecast especifico
     *
     */

    public function testForecastReturnSuccessWithExistentForecast()
    {
        $newForecast = new ForecastService();

        $forecast = $newForecast->getById(1); // ID não exista;

        $formatedForescast = json_decode($forecast->getContent(), true);
        $this->assertEquals($formatedForescast['error'], false);
        $this->assertNotEmpty($formatedForescast['data']);
    }

    /**
     * @testdox Falha ao excluir Forecast especifico inexistente
     *
     */

    public function testForecastDeleteReturnErrorWithInexistentForecast()
    {
        $newForecast = new ForecastService();
        $forecast = $newForecast->delete(9999);

        $formatedForescast = json_decode($forecast->getContent(), true);
        $this->assertEquals($formatedForescast['error'], true);
        $this->assertEquals($formatedForescast['message'], 'Forecast not found.');
    }

    /**
     * @testdox Sucesso ao obter Forecast por created_at
     *
     */

    public function testForecastReturnSuccessGetForecastByCreatedAt()
    {
        $newForecast = new ForecastService();
        $dateToday = Carbon::now()->toDateString();
        $forecast = $newForecast->getForecastByCreatedAt($dateToday);
        

        $formatedForescast = json_decode($forecast->getContent(), true);
        $this->assertEquals($formatedForescast['error'], false);
        $this->assertEmpty($formatedForescast['data']);
    }

    /**
     * @testdox Sucesso ao obter Forecast por created_at mas sem forecast
     *
     */

     public function testForecastReturnSuccessGetEmptyForecastByCreatedAt()
     {
         $newForecast = new ForecastService();
         $dateToday = Carbon::now()->toDateString();
         $forecast = $newForecast->getForecastByCreatedAt($dateToday);
         
 
         $formatedForescast = json_decode($forecast->getContent(), true);
         $this->assertEquals($formatedForescast['error'], false);
         $this->assertEmpty($formatedForescast['data']);
     }

    /**
     * 
     * @testdox Sucesso excluir Forecast especifico
     *
     */

    public function testForecastReturnSuccessDeleteExistentForecast()
    {
        $newForecast = new ForecastService();
        $forecast = $newForecast->delete(1);

        $formatedForescast = json_decode($forecast->getContent(), true);
        $this->assertEquals($formatedForescast['error'], false);
        $this->assertNotEmpty($formatedForescast['message'], 'Forecast deleted successfully.');
    }
}
