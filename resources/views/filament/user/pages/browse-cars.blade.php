<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Search Cars
            </x-slot>

            <form wire:submit="filter">
                {{ $this->form }}

                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" class="ml-3">
                        {{ __('Filter Cars') }}
                    </x-filament::button>

                    <x-filament::button type="button" color="gray" wire:click="resetTable" class="ml-3">
                        {{ __('Reset Filters') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Available Cars
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
