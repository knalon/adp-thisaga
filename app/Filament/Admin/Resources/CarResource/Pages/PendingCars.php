<?php

namespace App\Filament\Admin\Resources\CarResource\Pages;

use App\Filament\Admin\Resources\CarResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class PendingCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('is_active', false);
    }

    public function getTitle(): string
    {
        return 'Pending Car Approvals';
    }
}
