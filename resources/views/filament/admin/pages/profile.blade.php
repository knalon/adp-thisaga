<x-filament-panels::page>
    <x-filament-forms::form wire:submit="submit">
        {{ $this->form }}

        <div class="mt-4">
            {{ $this->getFormActions() }}
        </div>
    </x-filament-forms::form>
</x-filament-panels::page>
