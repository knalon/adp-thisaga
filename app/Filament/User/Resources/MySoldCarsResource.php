<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MySoldCarsResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MySoldCarsResource extends Resource
{
    protected static ?string $model = Car::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?string $navigationLabel = 'Sold Cars';
    protected static ?string $slug = 'sold-cars';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        // Get cars that the user has sold (the user is the car owner and there's a paid transaction)
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->whereHas('transactions', function (Builder $query) {
                $query->where('status', 'paid');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Car Details')
                    ->schema([
                        Forms\Components\TextInput::make('make')
                            ->disabled(),
                        Forms\Components\TextInput::make('model')
                            ->disabled(),
                        Forms\Components\TextInput::make('registration_year')
                            ->disabled(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('mileage')
                            ->numeric()
                            ->suffix('miles')
                            ->disabled(),
                        Forms\Components\ColorPicker::make('color')
                            ->disabled(),
                    ]),
                
                Forms\Components\Section::make('Sale Information')
                    ->schema([
                        Forms\Components\TextInput::make('transactions.0.final_price')
                            ->label('Sale Price')
                            ->disabled()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('transactions.0.transaction_reference')
                            ->label('Transaction Reference')
                            ->disabled(),
                        Forms\Components\TextInput::make('transactions.0.created_at')
                            ->label('Sale Date')
                            ->disabled(),
                        Forms\Components\TextInput::make('transactions.0.user.name')
                            ->label('Purchased By')
                            ->disabled(),
                        Forms\Components\TextInput::make('transactions.0.user.email')
                            ->label('Buyer Email')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_year')
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('transactions.0.final_price')
                    ->label('Sale Price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactions.0.created_at')
                    ->label('Sale Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactions.0.user.name')
                    ->label('Purchased By')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ])
            ->defaultSort('transactions.0.created_at', 'desc');
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
            'index' => Pages\ListMySoldCars::route('/'),
            'view' => Pages\ViewMySoldCar::route('/{record}'),
        ];
    }
} 