<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/prefectures', [SearchController::class, 'getPrefectures'])->name('getPrefectures');
Route::get('/regions', [SearchController::class, 'getRegions'])->name('getRegions');
Route::get('/regionWeather', [SearchController::class, 'getRegionWeather'])->name('getRegionWeather');

// Route::get('/httpTest', [SearchController::class, 'test'])->name('test');
