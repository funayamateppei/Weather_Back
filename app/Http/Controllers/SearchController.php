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
