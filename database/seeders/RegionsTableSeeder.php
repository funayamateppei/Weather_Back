<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $dummyData = [];

        for ($i = 0; $i < 30; $i++) {
            $dummyData[] = [
                'prefecture_id' => $faker->numberBetween(1, 58),
                'region' => 'Region ' . ($i + 1),
                'weather_id' => $faker->numberBetween(1, 118),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ];
        }

        DB::table('regions')->insert($dummyData);
    }
}
