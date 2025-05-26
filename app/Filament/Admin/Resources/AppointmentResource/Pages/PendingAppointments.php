<?php

namespace App\Filament\Admin\Resources\AppointmentResource\Pages;

use App\Filament\Admin\Resources\AppointmentResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class PendingAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (Builder $query) => $query->where('status', 'pending'));
    }

    public function getTitle(): string
    {
        return 'Pending Appointments';
    }
}
