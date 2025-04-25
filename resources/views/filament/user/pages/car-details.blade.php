<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Car Images -->
            <div class="md:col-span-2 space-y-4">
                @if(count($images) > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden h-[400px]">
                        <img src="{{ $images[0]->getUrl() }}" alt="{{ $car->make }} {{ $car->model }}" class="w-full h-full object-cover">
                    </div>
                    
                    @if(count($images) > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($images->skip(1)->take(4) as $image)
                                <div class="bg-white rounded-lg shadow overflow-hidden h-20">
                                    <img src="{{ $image->getUrl() }}" alt="{{ $car->make }} {{ $car->model }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="bg-gray-100 rounded-lg shadow flex items-center justify-center h-[400px]">
                        <x-heroicon-o-photo class="h-24 w-24 text-gray-400" />
                    </div>
                @endif
            </div>
            
            <!-- Car Details -->
            <div class="space-y-6">
                <x-filament::section>
                    <x-slot name="heading">
                        <h2 class="text-2xl font-bold">{{ $car->make }} {{ $car->model }}</h2>
                        <p class="text-xl font-bold text-primary-600 mt-1">${{ number_format($car->price) }}</p>
                    </x-slot>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Year</p>
                                <p class="font-medium">{{ $car->registration_year }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Mileage</p>
                                <p class="font-medium">{{ number_format($car->mileage) }} miles</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Transmission</p>
                                <p class="font-medium">{{ ucfirst($car->transmission) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Fuel Type</p>
                                <p class="font-medium">{{ ucfirst($car->fuel_type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Color</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $car->color }};"></div>
                                    <p class="font-medium">{{ $car->color }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Seller</p>
                                <p class="font-medium">{{ $car->user->name }}</p>
                            </div>
                        </div>
                        
                        <div class="pt-2">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p>{{ $car->description }}</p>
                        </div>
                    </div>
                </x-filament::section>
            </div>
        </div>
        
        <!-- Schedule Test Drive Form -->
        <x-filament::section>
            <form wire:submit="scheduleDrive">
                @php
                    echo $this->form;
                @endphp
                
                <div class="mt-4">
                    <x-filament::button type="submit" size="lg">
                        Schedule Test Drive
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page> 