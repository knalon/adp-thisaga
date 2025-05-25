<?php

namespace App\Filament\Admin\Resources\AppointmentResource\Pages;

use App\Filament\Admin\Resources\AppointmentResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class PendingAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->where('status', 'pending');
    }

    public function getTitle(): string
    {
        return 'Pending Appointments';
    }
}
