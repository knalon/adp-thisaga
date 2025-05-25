<?php

namespace App\Filament\Admin\Resources\BidResource\Pages;

use App\Filament\Admin\Resources\BidResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ApprovedBids extends ListRecords
{
    protected static string $resource = BidResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('status', 'accepted');
    }

    public function getTitle(): string
    {
        return 'Approved Bids';
    }
}
