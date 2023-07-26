<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Log;

use App\Models\Prefecture;

class SearchController extends Controller
{
    public function getPrefectures(Request $request)
    {
        $data = Prefecture::orderBy('id', 'asc')->get();
        return response()->json($data);
    }

    public function getRegions(Request $request)
    {
        // 取得するデータが存在するかの確認
        // $today = Carbon::today()->toDateString();
        // $prefecture_code = $request['prefecture_code'];
        // $data = Prefecture::with(['regions' => function ($query) use ($today) {
        //     $query->whereDate('created_at', $today);
        // }])->where('prefecture_code', $prefecture_code)->orderBy('id', 'asc')->get();

        // 存在していればそのままデータを返却する

        // 存在していなければ外部APIを呼び出し、取得してDBに保存する

        // 今日の該当するデータをDBから取得する関数
        $today = Carbon::today()->toDateString();
        $prefecture_code = $request['prefecture_code'];
        $data = Prefecture::with(['regions' => function ($query) use ($today) {
            $query->whereDate('created_at', $today)
                ->with('weather');
        }])->where('prefecture_code', $prefecture_code)->orderBy('id', 'asc')->first();

        // Log::debug($data);
        // Log::debug($today);
        return response()->json($data);
    }
}
