<?php

namespace App\Filament\User\Resources\PurchasedCarsResource\Pages;

use App\Filament\User\Resources\PurchasedCarsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchasedCar extends ViewRecord
{
    protected static string $resource = PurchasedCarsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
} 