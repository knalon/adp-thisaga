<?php

namespace App\Filament\Admin\Resources\BidResource\Pages;

use App\Filament\Admin\Resources\BidResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class PendingBids extends ListRecords
{
    protected static string $resource = BidResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('status', 'pending');
    }

    public function getTitle(): string
    {
        return 'Pending Bids';
    }
}
