<?php

namespace App\Filament\User\Resources\MyAppointmentResource\Pages;

use App\Filament\User\Resources\MyAppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAppointments extends ListRecords
{
    protected static string $resource = MyAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 