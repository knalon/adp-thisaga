<?php

namespace App\Filament\Admin\Resources\CarResource\Pages;

use App\Filament\Admin\Resources\CarResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ActiveCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('is_active', true);
    }

    public function getTitle(): string
    {
        return 'Active Car Listings';
    }
}
