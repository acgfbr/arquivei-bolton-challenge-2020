<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Api')->group(function () {
    Route::get('/nfe/{access_key?}', ['uses' => 'ApiNfeController']);
});