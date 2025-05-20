<?php

namespace App\Filament\User\Resources\SoldCarsResource\Pages;

use App\Filament\User\Resources\SoldCarsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSoldCar extends ViewRecord
{
    protected static string $resource = SoldCarsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
} 