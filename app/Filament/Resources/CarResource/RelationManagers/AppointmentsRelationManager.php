<?php

namespace App\Filament\Resources\CarResource\RelationManagers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Customer'),
                Forms\Components\DateTimePicker::make('appointment_date')
                    ->required()
                    ->minDate(now())
                    ->seconds(false),
                Forms\Components\TextInput::make('bid_price')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid_price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        'completed' => 'info',
                        'canceled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['car_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $appointment) => $appointment->status === 'pending')
                    ->action(function (Appointment $appointment) {
                        $previousStatus = $appointment->status;
                        $appointment->status = 'approved';
                        $appointment->save();

                        // Notify the user
                        $appointment->user->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $previousStatus));

                        // Log the action
                        ActivityLog::log(
                            'Approved appointment',
                            'appointment_approve',
                            $appointment,
                            [
                                'appointment_id' => $appointment->id,
                                'car_id' => $appointment->car_id,
                                'user_id' => $appointment->user_id,
                            ]
                        );
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $appointment) => $appointment->status === 'pending')
                    ->action(function (Appointment $appointment) {
                        $previousStatus = $appointment->status;
                        $appointment->status = 'rejected';
                        $appointment->save();

                        // Notify the user
                        $appointment->user->notify(new \App\Notifications\AppointmentStatusChanged($appointment, $previousStatus));

                        // Log the action
                        ActivityLog::log(
                            'Rejected appointment',
                            'appointment_reject',
                            $appointment,
                            [
                                'appointment_id' => $appointment->id,
                                'car_id' => $appointment->car_id,
                                'user_id' => $appointment->user_id,
                            ]
                        );
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('appointment_date', 'desc');
    }
}
