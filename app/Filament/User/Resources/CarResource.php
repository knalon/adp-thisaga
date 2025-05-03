<?php

namespace App\Filament\User\Resources;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Filament\User\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?string $navigationLabel = 'My Cars';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('make')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 1),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('mileage')
                    ->required()
                    ->numeric()
                    ->suffix('miles'),
                Forms\Components\Select::make('transmission')
                    ->options(collect(TransmissionType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                    ->required(),
                Forms\Components\Select::make('fuel_type')
                    ->options(collect(FuelType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(collect(CarStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(1000),
                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->maxFiles(5)
                    ->directory('cars'),
            ]);
    }

    public static function table(Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transmission')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->searchable(),
                Tables\Columns\TextColumn::make('fuel_type')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color())
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transmission')
                    ->options(collect(TransmissionType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])),
                Tables\Filters\SelectFilter::make('fuel_type')
                    ->options(collect(FuelType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])),
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(CarStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
