<?php

namespace App\Filament\User\Resources\MyListingResource\Pages;

use App\Filament\User\Resources\MyListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyListing extends ViewRecord
{
    protected static string $resource = MyListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
} 