<x-filament-panels::page>
    <x-filament-panels::form wire:submit="submit">
        {{ $form }}

        <x-filament::button type="submit" class="mt-6">
            Save Settings
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
