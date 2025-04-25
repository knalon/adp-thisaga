<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyListingResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Collection;

class MyListingResource extends Resource
{
    protected static ?string $model = Car::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'My Cars';
    protected static ?string $navigationLabel = 'My Listings';
    protected static ?string $slug = 'my-listings';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Car Details')
                    ->schema([
                        Forms\Components\TextInput::make('make')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('registration_year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(1),
                        Forms\Components\TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->suffix('miles')
                            ->minValue(0),
                        Forms\Components\ColorPicker::make('color')
                            ->required(),
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
                            ->required(),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'hybrid' => 'Hybrid',
                                'electric' => 'Electric',
                                'lpg' => 'LPG',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Car Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('car_images')
                            ->collection('car_images')
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Listing Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Inactive listings will not be visible to buyers')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Listed On')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Listings')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Listings')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListMyListings::route('/'),
            'create' => Pages\CreateMyListing::route('/create'),
            'edit' => Pages\EditMyListing::route('/{record}/edit'),
            'view' => Pages\ViewMyListing::route('/{record}'),
        ];
    }
} 