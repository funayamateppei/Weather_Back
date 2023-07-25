<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeatherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // txtファイルのパスを指定
        $filePath = public_path('weather_code.txt');

        // ファイルの内容を取得
        $fileContents = file_get_contents($filePath);

        // ファイル内容を配列に変換
        $data = json_decode($fileContents, true);

        if (!empty($data)) {
            foreach ($data as $code => $weatherData) {
                DB::table('weather')->insert([
                    'code' => $code, // keyを使用
                    'weather' => $weatherData[3], // 配列の4番目の値を使用
                    'image_code' => $weatherData[0] // 配列の0番目の値を使用
                ]);
            }
        }
    }
}
