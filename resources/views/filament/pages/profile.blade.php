<x-filament-panels::page>
    <x-filament-forms::form wire:submit="save">
        {{ $this->form }}

        <x-filament::button type="submit">
            Save
        </x-filament::button>
    </x-filament-forms::form>
</x-filament-panels::page>
