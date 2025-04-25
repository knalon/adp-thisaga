<?php

namespace App\Filament\User\Resources\MyBidResource\Pages;

use App\Filament\User\Resources\MyBidResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyBid extends ViewRecord
{
    protected static string $resource = MyBidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn ($record) => $record->status === 'pending'),
        ];
    }
}
