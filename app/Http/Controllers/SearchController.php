<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Prefecture;

class SearchController extends Controller
{
    public function getPrefectures(Request $request)
    {
        $data = Prefecture::orderBy('id', 'asc')->get();
        return response()->json($data);
    }

    // public function getRegions(Request $request)
    // {
    //     $prefecture_code = $request['prefecture_code'];
    //     $data = Prefecture::with('regions')->where([['prefecture_code', $prefecture_code], []])->orderBy('id', 'asc')->get();
    //     Log::debug($data);
    //     return response()->json($data);
    // }
}
