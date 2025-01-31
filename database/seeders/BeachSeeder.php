<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('beaches')->insert([
            'beach_name' => 'Navegantes',
            'latitude' => '-26.8989',
            'longitude' => '-48.6542',
            'created_at' => Carbon::now()
        ]);
    }
}
