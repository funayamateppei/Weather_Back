<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class PrefectureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // txtファイルのパスを指定
        $filePath = public_path('prefecture_code.txt');

        // ファイルの内容を取得
        $fileContents = file_get_contents($filePath);

        // ファイル内容を配列に変換
        $data = json_decode($fileContents, true);

        Log::debug($data);

        if (!empty($data)) {
            foreach ($data as $code => $prefectureData) {
                DB::table('prefecture')->insert([
                    'prefecture' => $prefectureData[1], // 配列の1番目の値を使用
                    'group' => $prefectureData[0], // 配列の0番目の値を使用
                    'prefecture_code' => $code, // keyを使用
                ]);
            }
        }
    }
}
