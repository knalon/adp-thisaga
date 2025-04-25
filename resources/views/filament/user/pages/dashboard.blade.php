<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-filament::section>
            <x-slot name="heading">My Cars</x-slot>
            <div class="flex flex-col items-center justify-center py-6">
                <div class="rounded-full bg-primary-500/10 p-3 mb-4">
                    <x-heroicon-o-truck class="h-6 w-6 text-primary-500" />
                </div>
                <p class="text-sm text-center">Manage your car listings from a central dashboard. Add new cars, edit existing ones, or remove listings.</p>
                <x-filament::button
                    tag="a"
                    href="/dashboard/my-listings"
                    class="mt-4"
                >
                    View Listings
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Bids & Appointments</x-slot>
            <div class="flex flex-col items-center justify-center py-6">
                <div class="rounded-full bg-warning-500/10 p-3 mb-4">
                    <x-heroicon-o-calendar class="h-6 w-6 text-warning-500" />
                </div>
                <p class="text-sm text-center">Track your bids and schedule test drives with sellers. Manage all your appointments from here.</p>
                <x-filament::button
                    tag="a"
                    href="/dashboard/appointments"
                    color="warning"
                    class="mt-4"
                >
                    Manage Appointments
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Transactions</x-slot>
            <div class="flex flex-col items-center justify-center py-6">
                <div class="rounded-full bg-success-500/10 p-3 mb-4">
                    <x-heroicon-o-banknotes class="h-6 w-6 text-success-500" />
                </div>
                <p class="text-sm text-center">View your transaction history, download invoices, and track your purchases and sales.</p>
                <x-filament::button
                    tag="a"
                    href="/dashboard/transactions"
                    color="success"
                    class="mt-4"
                >
                    View Transactions
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <x-filament::section>
            <x-slot name="heading">Purchased Cars</x-slot>
            <div class="flex flex-col items-center justify-center py-6">
                <div class="rounded-full bg-info-500/10 p-3 mb-4">
                    <x-heroicon-o-shopping-bag class="h-6 w-6 text-info-500" />
                </div>
                <p class="text-sm text-center">View all the cars you have purchased. Access vehicle details, transaction history, and ownership documents.</p>
                <x-filament::button
                    tag="a"
                    href="/dashboard/purchased-cars"
                    color="info"
                    class="mt-4"
                >
                    View Purchased Cars
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Sold Cars</x-slot>
            <div class="flex flex-col items-center justify-center py-6">
                <div class="rounded-full bg-success-500/10 p-3 mb-4">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-success-500" />
                </div>
                <p class="text-sm text-center">View all the cars you have sold. Access sale details, buyer information, and transaction receipts.</p>
                <x-filament::button
                    tag="a"
                    href="/dashboard/sold-cars"
                    color="success"
                    class="mt-4"
                >
                    View Sold Cars
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    @php
    $hasHeaderWidgets = $this->hasHeaderWidgets;
    $hasFooterWidgets = $this->hasFooterWidgets;
    @endphp

    @if ($hasHeaderWidgets)
        <x-filament-widgets::widgets
            :columns="$this->getHeaderWidgetsColumns()"
            :data="$this->getHeaderWidgetsData()"
            :widgets="$this->getVisibleHeaderWidgets()"
            class="mt-6"
        />
    @endif

    @if ($hasFooterWidgets)
        <x-filament-widgets::widgets
            :columns="$this->getFooterWidgetsColumns()"
            :data="$this->getFooterWidgetsData()"
            :widgets="$this->getVisibleFooterWidgets()"
            class="mt-6"
        />
    @endif
</x-filament-panels::page> 