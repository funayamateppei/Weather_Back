<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

use App\Models\Prefecture;
use App\Models\Region;
use App\Models\Weather;

use function Psy\debug;

class SearchController extends Controller
{
    public function getPrefectures(Request $request)
    {
        $data = Prefecture::orderBy('id', 'asc')->get();
        return response()->json($data);
    }

    public function getRegions(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $prefecture_code = $request['prefecture_code'];

        // 本日の該当するデータをDBから取得する
        $regionsCount = Prefecture::where('prefecture_code', $prefecture_code)
            ->withCount(['regions' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            }])
            ->first();
        // Log::debug($regionsCount);

        // regions_countが0以上の場合、データを返却する
        if ($regionsCount->regions_count > 0) {
            $data = Prefecture::where('prefecture_code', $prefecture_code)
                ->with(['regions' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->with('weather');
                }])
                ->first();
        } else {
            // regions_countが0以下の場合、外部APIを呼び出しデータを取得してDBに保存する
            $apiUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$prefecture_code}.json";
            $response = Http::get($apiUrl);
            // Log::debug($response);
            if ($response->successful()) { // HTTP通信が成功したらDBに保存しデータを返却する
                $prefecture_id = Prefecture::where('prefecture_code', $prefecture_code)->first()->id;
                foreach ($response[0]['timeSeries'][0]['areas'] as $areaData) {
                    $weather_id = Weather::where('weather_code', $areaData['weatherCodes'][0])->first()->id;
                    $regionData = [
                        'prefecture_id' => $prefecture_id,
                        'region' => $areaData['area']['name'],
                        'weather_id' => $weather_id,
                    ];
                    $result = Region::create($regionData);
                }
                $data = Prefecture::where('prefecture_code', $prefecture_code)
                    ->with(['regions' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->with('weather');
                    }])
                    ->first();
            } else {
                return response()->json($response);
            }
        }
        Log::debug($data);
        return response()->json($data);
    }

    public function getRegionWeather(Request $request)
    {
        $prefecture_code = $request['prefecture_code'];
        $region_name = $request['region_name'];

        $apiUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/$prefecture_code.json";
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $filteredAreaData = array_filter($response[0]['timeSeries'][0]['areas'], function ($item) use ($region_name) {
                return $item['area']['name'] === $region_name;
            });
            $filteredData = array_values($filteredAreaData);
            // Log::debug($filteredData);
            // "weatherCodes"の順番通りにデータを取得し、weather_codeを入れ替える
            foreach ($filteredData[0]['weatherCodes'] as $index => $code) {
                $weatherData = Weather::where('weather_code', $code)->first();
                if ($weatherData) {
                    $filteredData[0]['weatherCodes'][$index] = $weatherData;
                    $DateString = $response[0]['timeSeries'][0]['timeDefines'][$index]; // 日付の表記変更
                    $carbonDate = Carbon::parse($DateString);
                    $formattedDate = $carbonDate->format('n/j');
                    $filteredData[0]['weatherCodes'][$index]['date'] = $formattedDate;
                }
            }
            $filteredAreaPop = array_filter($response[0]['timeSeries'][1]['areas'], function ($item) use ($region_name) {
                return $item['area']['name'] === $region_name;
            });
            $filteredPop = array_values($filteredAreaPop);
            while (count($filteredPop[0]['pops']) < 8) {
                array_unshift($filteredPop[0]['pops'], "-");
            }
            $filteredData[0]['pops'] = $filteredPop[0]['pops'];


            $weekWeather = $response[1]['timeSeries'][0]['areas'];
            // "weatherCodes"の順番通りにデータを取得し、weather_codeを入れ替える
            foreach ($weekWeather[0]['weatherCodes'] as $index => $code) {
                $weatherData = Weather::where('weather_code', $code)->first();
                if ($weatherData) {
                    $weekWeather[0]['weatherCodes'][$index] = $weatherData;
                    $weekWeather[0]['weatherCodes'][$index]['pop'] = $response[1]['timeSeries'][0]['areas'][0]['pops'][$index];
                    $DateString = $response[1]['timeSeries'][0]['timeDefines'][$index]; // 日付の表記変更
                    $carbonDate = Carbon::parse($DateString);
                    // $carbonDate->subDay();
                    $formattedDate = $carbonDate->format('n/j');
                    $weekWeather[0]['weatherCodes'][$index]['date'] = $formattedDate;
                }
            }

            $detailData = [
                'Detail' => $filteredData[0], // 本日と明日の天気情報と降水確率など
                'Week' => $weekWeather // 該当地域の１週間の天気情報
            ];
            // Log::debug($detailData);
            return response()->json($detailData);
        } else {
            return response()->json($response);
        }
    }

    // Laravel側からHTTP通信で外部APIを呼び出せるかテスト
    // public function test(Request $request)
    // {
    //     $prefecture_code = $request['prefecture_code'];

    //     $apiUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$prefecture_code}.json";
    //     $response = Http::get($apiUrl);

    //     if ($response->successful()) {
    //         return $jsonData = $response->json();
    //     }
    // }
}
