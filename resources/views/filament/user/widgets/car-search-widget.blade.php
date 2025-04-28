<x-filament::section>
    <div class="space-y-6">
        <h2 class="text-xl font-bold tracking-tight">Find Your Next Car</h2>

        <div class="flex justify-center">
            {{ $this->searchAction }}
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-4">Latest Cars</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($latestCars as $car)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                            @if($car->getFirstMediaUrl('car_images'))
                                <img src="{{ $car->getFirstMediaUrl('car_images') }}" alt="{{ $car->make }} {{ $car->model }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full bg-gray-200 dark:bg-gray-700">
                                    <x-heroicon-o-photo class="w-12 h-12 text-gray-400" />
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <h4 class="text-lg font-semibold">{{ $car->make }} {{ $car->model }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $car->registration_year }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-lg font-bold text-primary-600 dark:text-primary-400">${{ number_format($car->price, 0) }}</span>
                                <a href="{{ route('filament.user.resources.my-bid-resource.create', ['car_id' => $car->id]) }}" class="text-sm text-secondary-600 dark:text-secondary-400 hover:underline">
                                    Make Bid
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-6">
                        <p class="text-gray-500 dark:text-gray-400">No cars currently listed.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('filament.user.pages.browse-cars') }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                    View all available cars
                </a>
            </div>
        </div>
    </div>
</x-filament::section>
