<?php

namespace App\Filament\Admin\Resources\AppointmentResource\Pages;

use App\Filament\Admin\Resources\AppointmentResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class ApprovedAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (Builder $query) => $query->where('status', 'approved'));
    }

    public function getTitle(): string
    {
        return 'Approved Appointments';
    }
}
