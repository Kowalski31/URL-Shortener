<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\URLShortenerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('home', function(){
    return view('home');
});

Route::post('/shorten', [URLShortenerController::class, 'shortenURL'])->name('shorten.url');

Route::get('/{shortURL}', [URLShortenerController::class, 'redirectShortURL']);
