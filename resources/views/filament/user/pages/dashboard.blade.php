@php
    $heading = 'Dashboard';
@endphp

<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament-panels::page>
