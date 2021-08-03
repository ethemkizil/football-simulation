<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmulationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/teams', [SmulationController::class, 'teams']);
Route::get('/generate-fixture', [SmulationController::class, 'generateFixture']);
Route::get('/play-week', [SmulationController::class, 'playWeek']);
Route::get('/play-all-week', [SmulationController::class, 'playAllWeek']);
Route::get('/score-sheet', [SmulationController::class, 'scoreSheet']);
Route::post('/reset-data', [SmulationController::class, 'resetData']);
