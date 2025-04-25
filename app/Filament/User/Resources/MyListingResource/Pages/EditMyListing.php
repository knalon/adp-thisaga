<?php

namespace App\Filament\User\Resources\MyListingResource\Pages;

use App\Filament\User\Resources\MyListingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyListing extends EditRecord
{
    protected static string $resource = MyListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
} 