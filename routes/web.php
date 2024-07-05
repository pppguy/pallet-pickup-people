<?php

use Illuminate\Support\Facades\Route;

//catch all route
Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tail', function () {
    return view('tailwindwelcome');
});
