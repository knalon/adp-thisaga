<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;

class MyAppointments extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'My Appointments';
    protected static ?string $title = 'My Appointments';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.my-appointments';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->where('user_id', auth()->user()->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('car.title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('appointment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'primary' => 'completed',
                        'secondary' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Appointment $record): string => route('filament.resources.appointments.edit', $record)),
                Tables\Actions\Action::make('cancel')
                    ->requiresConfirmation()
                    ->action(fn (Appointment $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (Appointment $record) => in_array($record->status, ['pending', 'approved'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 