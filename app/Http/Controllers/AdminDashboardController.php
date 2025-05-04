<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class AdminDashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('filament.admin.pages.dashboard');
    }
}
