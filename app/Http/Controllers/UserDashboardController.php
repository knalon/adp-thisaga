<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class UserDashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('filament.user.pages.dashboard');
    }
}
