<?php

namespace App\Filament\User\Resources\BidResource\Pages;

use App\Filament\User\Resources\BidResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBid extends EditRecord
{
    protected static string $resource = BidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 