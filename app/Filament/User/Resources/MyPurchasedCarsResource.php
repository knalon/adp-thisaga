<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyPurchasedCarsResource\Pages;
use App\Models\Car;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyPurchasedCarsResource extends Resource
{
    protected static ?string $model = Car::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?string $navigationLabel = 'Purchased Cars';
    protected static ?string $slug = 'purchased-cars';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Get cars that the user has purchased (from completed transactions)
        return parent::getEloquentQuery()
            ->whereHas('transactions', function (Builder $query) {
                $query->where('user_id', Auth::id())
                    ->where('status', 'paid');
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
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('transmission')
                            ->options([
                                'automatic' => 'Automatic',
                                'manual' => 'Manual',
                                'cvt' => 'CVT',
                                'semi-automatic' => 'Semi-Automatic',
                            ])
                            ->disabled(),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'hybrid' => 'Hybrid',
                                'electric' => 'Electric',
                                'lpg' => 'LPG',
                                'other' => 'Other',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Purchase Information')
                    ->schema([
                        Forms\Components\TextInput::make('transactions.0.final_price')
                            ->label('Purchase Price')
                            ->disabled()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('transactions.0.transaction_reference')
                            ->label('Transaction Reference')
                            ->disabled(),
                        Forms\Components\TextInput::make('transactions.0.created_at')
                            ->label('Purchase Date')
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
                Tables\Columns\TextColumn::make('transactions')
                    ->label('Purchase Price')
                    ->getStateUsing(function (Car $record) {
                        $transaction = $record->transactions()
                            ->where('user_id', Auth::id())
                            ->where('status', 'paid')
                            ->first();
                        
                        return $transaction ? $transaction->final_price : null;
                    })
                    ->money('USD'),
                Tables\Columns\TextColumn::make('transactions')
                    ->label('Purchase Date')
                    ->getStateUsing(function (Car $record) {
                        $transaction = $record->transactions()
                            ->where('user_id', Auth::id())
                            ->where('status', 'paid')
                            ->first();
                        
                        return $transaction ? $transaction->created_at : null;
                    })
                    ->dateTime(),
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
            ->defaultSort('transactions.created_at', 'desc');
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
            'index' => Pages\ListMyPurchasedCars::route('/'),
            'view' => Pages\ViewMyPurchasedCar::route('/{record}'),
        ];
    }
} 