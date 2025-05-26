<?php

namespace App\Filament\Admin\Resources\BidResource\Pages;

use App\Filament\Admin\Resources\BidResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class PendingBids extends ListRecords
{
    protected static string $resource = BidResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (Builder $query) => $query->where('status', 'pending'));
    }

    public function getTitle(): string
    {
        return 'Pending Bids';
    }
}
