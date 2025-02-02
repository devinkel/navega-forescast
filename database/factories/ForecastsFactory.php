<?php
namespace Database\Factories;

use App\Models\Forecasts;
use App\Models\Beach;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForecastsFactory extends Factory
{
    protected $model = Forecasts::class;

    public function definition()
    {
        // Geração de dados de previsão em formato similar ao seu JSON
        $forecast = [];
        $start_date = Carbon::now()->format('Y-m-d');

        foreach (range(0, 23) as $hour) {
            $hour_formatted = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $forecast[$start_date ]["$hour_formatted:00"] = [
                'swell_wave_height' => $this->faker->randomFloat(2, 0.4, 1). 'm', // Exemplo de geração de swell_wave_height
                'wave_height' => $this->faker->randomFloat(2, 0.6, 1) . 'm',
                'wave_direction' => $this->faker->numberBetween(70, 110) . '°',
                'wave_period' => $this->faker->randomFloat(2, 5, 8) . 's',
                'swell_wave_direction' => $this->faker->numberBetween(70, 110) . '°',
                'swell_wave_period' => $this->faker->randomFloat(2, 5, 8) . 's',
            ];
        }

        $beaches = Beach::all(); // Ou Beach::limit(10)->get(); caso queira limitar o número de praias

        foreach ($beaches as $beach) {
            // Criar a previsão no banco de dados
            return [
                'beach_id' => $beach->id,
                'forecast' => $forecast,
            ];
        }
    }
}
