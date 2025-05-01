<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'make', 'model', 'year', 'min_price', 'max_price',
            'fuel_type', 'transmission', 'search', 'sort'
        ]);

        $query = Car::query()
            ->with(['images']);

        // Apply filters
        if (!empty($filters['make'])) {
            $query->where('make', $filters['make']);
        }

        if (!empty($filters['model'])) {
            $query->where('model', $filters['model']);
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (!empty($filters['fuel_type'])) {
            $query->where('fuel_type', $filters['fuel_type']);
        }

        if (!empty($filters['transmission'])) {
            $query->where('transmission', $filters['transmission']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('make', 'like', "%{$searchTerm}%")
                    ->orWhere('model', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'year_asc':
                    $query->orderBy('year', 'asc');
                    break;
                case 'year_desc':
                    $query->orderBy('year', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $cars = $query->paginate(9)->withQueryString();

        // Add primary image URL to each car
        $cars->getCollection()->transform(function ($car) {
            $car->primary_image = $car->images->isNotEmpty()
                ? $car->images->first()->path
                : null;

            return $car;
        });

        // Get distinct makes, years, fuel types, and transmissions for filters
        $makes = Car::distinct()->pluck('make')->filter()->values();
        $years = Car::distinct()->orderBy('year', 'desc')->pluck('year')->filter()->values();
        $fuelTypes = Car::distinct()->pluck('fuel_type')->filter()->values();
        $transmissions = Car::distinct()->pluck('transmission')->filter()->values();

        // Get price range for filters
        $priceRange = [
            'min' => Car::min('price') ?: 0,
            'max' => Car::max('price') ?: 1000000
        ];

        return Inertia::render('Cars/Index', [
            'cars' => $cars,
            'filters' => $filters,
            'makes' => $makes,
            'years' => $years,
            'priceRange' => $priceRange,
            'fuelTypes' => $fuelTypes,
            'transmissions' => $transmissions,
        ]);
    }

    /**
     * Display the car details.
     */
    public function show($id)
    {
        $car = Car::with(['images', 'user'])->findOrFail($id);

        // Get similar cars (same make or model)
        $similarCars = Car::where('id', '!=', $car->id)
            ->where(function($query) use ($car) {
                $query->where('make', $car->make)
                      ->orWhere('model', $car->model);
            })
            ->with(['images'])
            ->limit(3)
            ->get();

        // Add primary image to similar cars
        $similarCars->transform(function ($car) {
            $car->primary_image = $car->images->isNotEmpty()
                ? $car->images->first()->path
                : null;

            return $car;
        });

        return Inertia::render('Cars/Show', [
            'car' => $car,
            'similarCars' => $similarCars,
        ]);
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to list a car.');
        }

        return Inertia::render('Cars/Create');
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'make' => 'required|string|max:50',
                'model' => 'required|string|max:50',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'price' => 'required|numeric|min:0',
                'mileage' => 'nullable|numeric|min:0',
                'fuel_type' => 'nullable|string|max:50',
                'transmission' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:30',
                'description' => 'required|string',
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Create car record
            $car = new Car();
            $car->user_id = Auth::id();
            $car->make = $validated['make'];
            $car->model = $validated['model'];
            $car->year = $validated['year'];
            $car->price = $validated['price'];
            $car->mileage = $validated['mileage'] ?? null;
            $car->fuel_type = $validated['fuel_type'] ?? null;
            $car->transmission = $validated['transmission'] ?? null;
            $car->color = $validated['color'] ?? null;
            $car->description = $validated['description'];
            $car->status = 'pending';
            $car->is_approved = false;
            $car->is_active = false;
            $car->save();

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('car-images', 'public');
                    $car->images()->create([
                        'path' => $path,
                        'is_primary' => $car->images()->count() === 0 // First image is primary
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('user.cars')->with('success', 'Car listing submitted successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Car creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create car listing. Please try again.'])->withInput();
        }
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit($id)
    {
        $car = Car::with('images')->findOrFail($id);

        // Check if user is authorized to edit this car
        if (Auth::id() !== $car->user_id && !Auth::user()->is_admin) {
            return redirect()->route('cars.show', $car->id)
                ->with('error', 'You are not authorized to edit this listing.');
        }

        return Inertia::render('Cars/Edit', [
            'car' => $car,
        ]);
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        // Check if user is authorized to update this car
        if (Auth::id() !== $car->user_id && !Auth::user()->is_admin) {
            return redirect()->route('cars.show', $car->id)
                ->with('error', 'You are not authorized to edit this listing.');
        }

        // Validate request
        $validated = $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'mileage' => 'nullable|numeric|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:30',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
        ]);

        // Update car record
        $car->update($validated);

        // Handle image removal
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageId) {
                $image = $car->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('car-images', 'public');
                $car->images()->create([
                    'path' => $path,
                    'type' => 'image',
                ]);
            }
        }

        return redirect()->route('cars.show', $car->id)
            ->with('success', 'Car updated successfully!');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        // Check if user is authorized to delete this car
        if (Auth::id() !== $car->user_id && !Auth::user()->is_admin) {
            return redirect()->route('cars.show', $car->id)
                ->with('error', 'You are not authorized to delete this listing.');
        }

        // Delete images from storage
        foreach ($car->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        // Delete the car record
        $car->delete();

        return redirect()->route('cars.index')
            ->with('success', 'Car listing deleted successfully!');
    }

    /**
     * Get models for a specific make (for dynamic filtering)
     */
    public function getModelsByMake(Request $request)
    {
        $make = $request->input('make');

        if (!$make) {
            return response()->json([]);
        }

        $models = Car::where('make', $make)
            ->distinct()
            ->pluck('model')
            ->values();

        return response()->json($models);
    }
}
