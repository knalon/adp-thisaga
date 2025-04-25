<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Audit Log';
    protected static ?string $pluralLabel = 'Audit Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id')
                    ->label('User ID')
                    ->disabled(),
                TextInput::make('action')
                    ->disabled(),
                TextInput::make('action_type')
                    ->disabled(),
                TextInput::make('subject_type')
                    ->disabled(),
                TextInput::make('subject_id')
                    ->disabled(),
                Textarea::make('properties')
                    ->json()
                    ->columnSpanFull()
                    ->disabled(),
                TextInput::make('ip_address')
                    ->disabled(),
                TextInput::make('user_agent')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('created_at')
                    ->label('Logged at')
                    ->disabled(),
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
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user_create' => 'success',
                        'user_update' => 'info',
                        'user_delete' => 'danger',
                        'user_activate' => 'success',
                        'user_deactivate' => 'warning',
                        'car_create' => 'success',
                        'car_update' => 'info',
                        'car_delete' => 'danger',
                        'car_approve' => 'success',
                        'car_reject' => 'warning',
                        'appointment_create' => 'success',
                        'appointment_update' => 'info',
                        'appointment_delete' => 'danger',
                        'appointment_approve' => 'success',
                        'appointment_reject' => 'warning',
                        'transaction_create' => 'success',
                        'transaction_update' => 'info',
                        'transaction_delete' => 'danger',
                        'transaction_paid' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return 'N/A';
                        $parts = explode('\\', $state);
                        return end($parts);
                    })
                    ->sortable(),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Logged at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action_type')
                    ->options([
                        'user_create' => 'User Created',
                        'user_update' => 'User Updated',
                        'user_delete' => 'User Deleted',
                        'user_activate' => 'User Activated',
                        'user_deactivate' => 'User Deactivated',
                        'car_create' => 'Car Created',
                        'car_update' => 'Car Updated',
                        'car_delete' => 'Car Deleted',
                        'car_approve' => 'Car Approved',
                        'car_reject' => 'Car Rejected',
                        'appointment_create' => 'Appointment Created',
                        'appointment_update' => 'Appointment Updated',
                        'appointment_delete' => 'Appointment Deleted',
                        'appointment_approve' => 'Appointment Approved',
                        'appointment_reject' => 'Appointment Rejected',
                        'transaction_create' => 'Transaction Created',
                        'transaction_update' => 'Transaction Updated',
                        'transaction_delete' => 'Transaction Deleted',
                        'transaction_paid' => 'Transaction Paid',
                    ]),
                SelectFilter::make('user')
                    ->relationship('user', 'name'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([])
            ->actions([]);
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
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
