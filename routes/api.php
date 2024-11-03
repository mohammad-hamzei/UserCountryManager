<?php


use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('api')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('/countries-with-currencies', [CountryController::class, 'getCountriesWithCurrencies']);
});
