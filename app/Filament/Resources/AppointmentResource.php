<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ActivityLog;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Carbon;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Appointments';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Appointment Details')
                    ->schema([
                        Select::make('user_id')
                    ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Customer'),
                        Select::make('car_id')
                            ->relationship('car', 'name')
                            ->searchable()
                            ->preload()
                    ->required(),
                        DateTimePicker::make('appointment_date')
                            ->required()
                            ->minDate(now())
                            ->helperText('Select a date and time for the appointment'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('bid_amount')
                    ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car.name')
                    ->label('Car')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        'completed' => 'info',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('bid_amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('appointment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('appointment_date', '<=', $date),
                            );
                    }),
                Filter::make('today')
                    ->label('Today\'s Appointments')
                    ->query(fn (Builder $query): Builder => $query->whereDate('appointment_date', Carbon::today())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $appointment) => $appointment->status === 'pending')
                    ->action(function (Appointment $appointment) {
                        $appointment->status = 'approved';
                        $appointment->save();

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
                        $appointment->status = 'rejected';
                        $appointment->save();

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
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $appointment) => $appointment->status === 'approved')
                    ->action(function (Appointment $appointment) {
                        $appointment->status = 'completed';
                        $appointment->save();

                        ActivityLog::log(
                            'Completed appointment',
                            'appointment_complete',
                            $appointment,
                            [
                                'appointment_id' => $appointment->id,
                                'car_id' => $appointment->car_id,
                                'user_id' => $appointment->user_id,
                            ]
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                ActivityLog::log(
                                    'Deleted appointment',
                                    'appointment_delete',
                                    null,
                                    [
                                        'appointment_id' => $record->id,
                                        'car_id' => $record->car_id,
                                        'user_id' => $record->user_id,
                                    ]
                                );
                            }
                            $records->each->delete();
                        }),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'approved';
                                    $record->save();
                                    ActivityLog::log(
                                        'Approved appointment',
                                        'appointment_approve',
                                        $record,
                                        [
                                            'appointment_id' => $record->id,
                                            'car_id' => $record->car_id,
                                            'user_id' => $record->user_id,
                                        ]
                                    );
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'rejected';
                                    $record->save();
                                    ActivityLog::log(
                                        'Rejected appointment',
                                        'appointment_reject',
                                        $record,
                                        [
                                            'appointment_id' => $record->id,
                                            'car_id' => $record->car_id,
                                            'user_id' => $record->user_id,
                                        ]
                                    );
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ? 'warning' : null;
    }
}
