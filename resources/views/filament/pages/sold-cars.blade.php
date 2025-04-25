<x-filament::page>
    <x-filament::section>
        <h2 class="text-xl font-semibold mb-4">Sold Cars Overview</h2>
        <p class="mb-6">This page displays all vehicles that have been sold through the platform. You can view transaction details, generate invoices, and track sales statistics.</p>
        
        @php
            echo $this->table;
        @endphp
    </x-filament::section>
</x-filament::page> 