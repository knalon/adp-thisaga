<?php

namespace App\Filament\Resources\BidResource\Pages;

use App\Filament\Resources\BidResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBid extends ViewRecord
{
    protected static string $resource = BidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
