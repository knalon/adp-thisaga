<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyAppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyAppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Bids & Appointments';
    protected static ?string $navigationLabel = 'My Appointments';
    protected static ?string $slug = 'appointments';
    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Appointment Details')
                    ->schema([
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'make', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('make')
                                    ->required(),
                                Forms\Components\TextInput::make('model')
                                    ->required(),
                                Forms\Components\TextInput::make('registration_year')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                        Forms\Components\DateTimePicker::make('appointment_date')
                            ->required()
                            ->minDate(now())
                            ->seconds(false),
                        Forms\Components\TextInput::make('bid_price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'completed' => 'Completed',
                                'canceled' => 'Canceled',
                            ])
                            ->default('pending')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car.make')
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.model')
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.price')
                    ->money('USD')
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
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        'canceled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record) => $record->status !== 'canceled')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'canceled'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false),
                ]),
            ])
            ->defaultSort('appointment_date', 'desc');
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
            'index' => Pages\ListMyAppointments::route('/'),
            'create' => Pages\CreateMyAppointment::route('/create'),
            'edit' => Pages\EditMyAppointment::route('/{record}/edit'),
            'view' => Pages\ViewMyAppointment::route('/{record}'),
        ];
    }
} 