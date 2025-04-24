<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::where('is_approved', true)
            ->where('is_active', true);

        // Filter by make
        if ($request->has('make') && $request->make !== '') {
            $query->where('make', $request->make);
        }

        // Filter by model
        if ($request->has('model') && $request->model !== '') {
            $query->where('model', $request->model);
        }

        // Filter by registration year
        if ($request->has('year') && $request->year !== '') {
            $query->where('registration_year', $request->year);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price !== '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price !== '') {
            $query->where('price', '<=', $request->max_price);
        }

        $cars = $query->paginate(12)->withQueryString();

        // Get unique makes and models for filter dropdowns
        $makes = Car::where('is_approved', true)
            ->where('is_active', true)
            ->distinct()
            ->pluck('make');

        // Get min and max price for range input
        $priceRange = Car::where('is_approved', true)
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        // Get unique years for filter dropdowns
        $years = Car::where('is_approved', true)
            ->where('is_active', true)
            ->distinct()
            ->orderBy('registration_year', 'desc')
            ->pluck('registration_year');

        return Inertia::render('Cars/Index', [
            'cars' => $cars,
            'filters' => $request->only(['make', 'model', 'year', 'min_price', 'max_price']),
            'makes' => $makes,
            'years' => $years,
            'priceRange' => $priceRange,
        ]);
    }

    public function show(Car $car)
    {
        if (!$car->is_approved || !$car->is_active) {
            abort(404);
        }

        $car->load('user');

        // Get the current highest bid for this car
        $currentBid = $car->appointments()
            ->where('status', 'approved')
            ->whereNotNull('bid_price')
            ->max('bid_price');

        // Get bid history for this car
        $bidHistory = $car->appointments()
            ->select('appointments.id', 'users.name as user_name', 'appointments.bid_price as amount', 'appointments.updated_at as created_at')
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->where('status', 'approved')
            ->whereNotNull('bid_price')
            ->orderBy('bid_price', 'desc')
            ->get();

        // Check if authenticated user has an appointment for this car
        $userAppointment = null;
        if (Auth::check()) {
            $userAppointment = $car->appointments()
                ->where('user_id', Auth::id())
                ->first();
        }

        return Inertia::render('Cars/Show', [
            'car' => $car,
            'carImages' => $car->getMedia('car_images')->map->getUrl(),
            'currentBid' => $currentBid,
            'bidHistory' => $bidHistory,
            'userAppointment' => $userAppointment,
        ]);
    }

    public function create()
    {
        return Inertia::render('Cars/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'registration_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:255',
            'mileage' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car = Auth::user()->cars()->create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $car->addMedia($image)->toMediaCollection('car_images');
            }
        }

        return redirect()->route('cars.show', $car->slug)->with('success', 'Car listing created and waiting for approval.');
    }

    public function edit(Car $car)
    {
        if (Auth::id() !== $car->user_id) {
            abort(403);
        }

        return Inertia::render('Cars/Edit', [
            'car' => $car,
            'carImages' => $car->getMedia('car_images')->map->getUrl(),
        ]);
    }

    public function update(Request $request, Car $car)
    {
        if (Auth::id() !== $car->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'registration_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:255',
            'mileage' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $car->addMedia($image)->toMediaCollection('car_images');
            }
        }

        return redirect()->route('cars.show', $car->slug)->with('success', 'Car listing updated successfully.');
    }

    public function destroy(Car $car)
    {
        if (Auth::id() !== $car->user_id) {
            abort(403);
        }

        $car->delete();

        return redirect()->route('dashboard')->with('success', 'Car listing deleted successfully.');
    }

    public function deactivate(Car $car)
    {
        if (Auth::id() !== $car->user_id) {
            abort(403);
        }

        $car->update(['is_active' => false]);

        return redirect()->route('dashboard')->with('success', 'Car listing deactivated successfully.');
    }
}
