<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Get featured cars for the navigation
        $featuredCars = [];

        // Only query for cars if the table exists
        if (Schema::hasTable('cars')) {
            $featuredCars = Car::where('is_approved', true)
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get();
        }

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $request->user(),
                'isAdmin' => $request->user() ? $request->user()->hasRole('admin') : false,
                'isBanned' => $request->user() ? $request->user()->is_banned : false,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'success' => [
                'message' => session('success'),
                'time' => microtime(true),
            ],
            'error' => session('error'),
            'featuredCars' => $featuredCars,
            'keyword' => $request->query('keyword'),
            'filters' => [
                'make' => $request->query('make'),
                'model' => $request->query('model'),
                'year' => $request->query('year'),
                'price_min' => $request->query('price_min'),
                'price_max' => $request->query('price_max'),
            ],
        ];
    }
}
