<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        // Here you would typically send an email or save to database
        // For example:
        // Mail::to('info@abccars.com')->send(new ContactFormSubmission($validated));

        return response()->json(['message' => 'Message sent successfully']);
    }

    public function dashboard()
    {
        $user = Auth::user();
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
