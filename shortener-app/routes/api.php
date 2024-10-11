<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLShortenerController;

Route::get('/get-urls', [URLShortenerController::class, 'getURLs'])->name('api.get.urls');

Route::post('/shorten', [URLShortenerController::class, 'shortenURL'])->name('api.shorten.url');

Route::get('/{shortURL}', [URLShortenerController::class, 'redirectShortURL']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
