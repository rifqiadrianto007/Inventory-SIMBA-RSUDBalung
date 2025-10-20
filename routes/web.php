<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('Home', [
        'title' => 'Halo dari Laravel + React + Inertia'
    ]);
});
