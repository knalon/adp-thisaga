<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="saveProfile">
            {{ $this->profileForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" class="ml-3">
                    {{ __('Save Profile') }}
                </x-filament::button>
            </div>
        </form>

        <x-filament::hr />

        <form wire:submit="changePassword">
            {{ $this->passwordForm }}

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit" class="ml-3">
                    {{ __('Change Password') }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
