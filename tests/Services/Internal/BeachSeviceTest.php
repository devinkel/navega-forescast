<?php

namespace Tests;

use App\Services\Internal\ForecastService;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ForecastSeviceTest extends TestCase
{

    use DatabaseTransactions;
    /**
     * @testdox --ForecastService : Falha ao criar forecast com beach_id inválido
     *        
     * @group Forecast
     */

    public function testForecastReturnErrorWithInvalidBeachId()
    {
        $invalidForecast =  [
            'beach_id' => 999, // ID não existe
            'forecast' => [
                '2025-02-01' =>[ 
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
     * @testdox --ForecastService : Falha ao criar forecast sem beach_id
     *        
     * @group Forecast
     */

    public function testForecastReturnErrorWithoutBeachId()
    {
        $invalidForecast =  [
            'forecast' => [
                '2025-02-01' =>[ 
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
     * @testdox --ForecastService : Sucesso ao criar um foracast
     *        
     * @group Forecast
     */

     public function testForecastSuccess()
     {
         $validForecast =  [
             'beach_id' => 1, // Supondo que esse ID não exista
             'forecast' => [
                 '2025-02-03' =>[ 
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
}
