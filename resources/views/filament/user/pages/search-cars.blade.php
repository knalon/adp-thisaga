<x-filament-panels::page>
    <form wire:submit="search">
        @php
            echo $this->form;
        @endphp

        <div class="mt-4 flex items-center justify-between">
            <x-filament::button type="submit" size="lg">
                Search Cars
            </x-filament::button>

            @if ($isSearchPerformed && count($results) > 0)
                <x-filament::button 
                    type="button" 
                    wire:click="saveSearch" 
                    color="gray"
                    icon="heroicon-m-bell-alert"
                >
                    Save Search & Get Notifications
                </x-filament::button>
            @endif
        </div>
    </form>

    @if ($isSearchPerformed)
        <div class="mt-8">
            <h2 class="text-xl font-bold">Search Results ({{ count($results) }} cars found)</h2>
            
            @if(count($results) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                    @foreach($results as $car)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                @if($car->getFirstMediaUrl('car_images'))
                                    <img src="{{ $car->getFirstMediaUrl('car_images') }}" alt="{{ $car->make }} {{ $car->model }}" class="object-cover h-full w-full">
                                @else
                                    <x-heroicon-o-photo class="h-16 w-16 text-gray-400" />
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-bold">{{ $car->make }} {{ $car->model }}</h3>
                                <p class="text-sm text-gray-600">{{ $car->registration_year }} • {{ number_format($car->mileage) }} miles</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-lg font-bold text-primary-600">${{ number_format($car->price) }}</span>
                                    <span class="text-sm text-gray-500">{{ ucfirst($car->transmission) }} • {{ ucfirst($car->fuel_type) }}</span>
                                </div>
                                <div class="mt-4 flex items-center space-x-2">
                                    <x-filament::button 
                                        tag="a" 
                                        href="{{ route('filament.user.resources.appointments.create', ['car_id' => $car->id]) }}" 
                                        color="warning"
                                        size="sm"
                                    >
                                        Schedule Test Drive
                                    </x-filament::button>
                                    <x-filament::button 
                                        tag="a" 
                                        href="/dashboard/car-details/{{ $car->id }}" 
                                        color="gray"
                                        size="sm"
                                    >
                                        View Details
                                    </x-filament::button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-8 text-center mt-4">
                    <x-heroicon-o-magnifying-glass class="h-12 w-12 text-gray-400 mx-auto" />
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No cars found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search filters to find more results.</p>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page> 