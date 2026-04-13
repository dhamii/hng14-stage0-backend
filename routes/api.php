<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/hi', function () {
    return "Okay";
});

Route::get('classify', [ApiController::class, 'getDetail']);