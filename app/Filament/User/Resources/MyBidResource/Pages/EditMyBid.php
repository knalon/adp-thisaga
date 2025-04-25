<?php

namespace App\Filament\User\Resources\MyBidResource\Pages;

use App\Filament\User\Resources\MyBidResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyBid extends EditRecord
{
    protected static string $resource = MyBidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
