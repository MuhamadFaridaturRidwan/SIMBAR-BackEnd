<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redoc', function () {
    return view('redoc');
});

Route::get('/openapi.yaml', function () {
    return redirect('/docs.openapi');
});
