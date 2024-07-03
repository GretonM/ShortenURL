<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

Route::post('/shorten', [UrlController::class, 'shorten']);
Route::get('/{hash}', [UrlController::class, 'redirect']);
