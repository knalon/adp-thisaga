<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\ContactMessage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch featured cars with media
        $featuredCars = Car::with('media')
            ->where('is_approved', true)
            ->where('is_active', true)
            ->where('is_sold', false)
            ->orderBy('created_at', 'desc')
            ->take(4)
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

        // Create a new contact message
        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'user_id' => Auth::id(), // Will be null for guests
        ]);

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you as soon as possible.');
    }

    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return Inertia::render('User/Dashboard');
    }
}
