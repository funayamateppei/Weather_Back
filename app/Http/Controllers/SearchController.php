<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Prefecture;

class SearchController extends Controller
{
    public function getPrefectures(Request $request)
    {
        $data = Prefecture::orderBy('id', 'asc')->get();
        return response()->json($data);
    }
}
