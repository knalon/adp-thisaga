<?php

namespace App\Filament\Admin\Resources\CarResource\Pages;

use App\Filament\Admin\Resources\CarResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class PendingCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (Builder $query) => $query->where('status', 'pending'));
    }

    public function getTitle(): string
    {
        return 'Pending Cars';
    }
}
