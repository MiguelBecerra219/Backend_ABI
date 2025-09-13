<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ResearchGroupController;
use App\Http\Controllers\InvestigationLineController;
use App\Http\Controllers\ThematicAreaController;

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

Route::apiResource('research-groups', ResearchGroupController::class);
Route::apiResource('programs', ProgramController::class);
Route::apiResource('investigation-lines', InvestigationLineController::class);
Route::apiResource('thematic-areas', ThematicAreaController::class);
