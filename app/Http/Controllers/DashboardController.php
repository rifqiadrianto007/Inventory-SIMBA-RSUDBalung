<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama
     */
    public function index()
    {
        return \Inertia\Inertia::render('Home', [
            'title' => 'Halo dari Laravel + React + Inertia'
        ]);
    }
}
