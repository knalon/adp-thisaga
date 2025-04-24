<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCars = Car::where('is_approved', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return Inertia::render('Home', [
            'featuredCars' => $featuredCars,
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $cars = $user->cars()->latest()->get();
        $appointments = $user->appointments()->latest()->get();
        $transactions = $user->transactions()->latest()->get();

        return Inertia::render('User/Dashboard', [
            'cars' => $cars,
            'appointments' => $appointments,
            'transactions' => $transactions,
        ]);
    }
}
