<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLShortenerController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('home', function(){
//     return view('home');
// });

// Route::get('/get-urls', [URLShortenerController::class, 'getURLs'])->name('api.get.urls');

// Route::post('/shorten', [URLShortenerController::class, 'shortenURL'])->name('api.shorten.url');

// Route::get('/{shortURL}', [URLShortenerController::class, 'redirectShortURL']);
