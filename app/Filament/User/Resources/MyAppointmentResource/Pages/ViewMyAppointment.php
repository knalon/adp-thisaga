<?php

namespace App\Filament\User\Resources\MyAppointmentResource\Pages;

use App\Filament\User\Resources\MyAppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyAppointment extends ViewRecord
{
    protected static string $resource = MyAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 