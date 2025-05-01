<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCars = Car::where('is_approved', true)
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Home', [
            'featuredCars' => $featuredCars
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
            'message' => 'required|string|max:500'
        ]);

        ContactMessage::create($validated);

        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
