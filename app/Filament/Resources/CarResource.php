<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ActivityLog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Car Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Car Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('make')
                    ->required()
                    ->maxLength(255),
                        TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                        Select::make('condition')
                            ->options([
                                'new' => 'New',
                                'used' => 'Used',
                                'certified' => 'Certified Pre-Owned',
                            ])
                            ->required(),
                        Textarea::make('description')
                    ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pricing and Details')
                    ->schema([
                        TextInput::make('price')
                    ->required()
                    ->numeric()
                            ->minValue(0)
                    ->prefix('$'),
                        TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('miles'),
                        TextInput::make('color')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('engine')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('transmission')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('fuel_type')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),

                Section::make('Images')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->image()
                            ->directory('cars/featured')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('gallery')
                            ->image()
                            ->multiple()
                            ->directory('cars/gallery')
                            ->maxFiles(10)
                    ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Approved')
                            ->default(false)
                            ->helperText('Only approved cars will be visible to users'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Toggle to hide/show this car listing'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                    ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->square()
                    ->label('Image'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Approved')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->options([
                        '1' => 'Approved',
                        '0' => 'Not Approved',
                    ])
                    ->label('Approval Status'),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Active Status'),
                SelectFilter::make('condition')
                    ->options([
                        'new' => 'New',
                        'used' => 'Used',
                        'certified' => 'Certified Pre-Owned',
                    ]),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Car $car) {
                        $car->is_approved = true;
                        $car->save();

                        ActivityLog::log(
                            'Approved car listing',
                            'car_approve',
                            $car,
                            ['car_id' => $car->id, 'car_name' => $car->name]
                        );
                    })
                    ->visible(fn (Car $car) => !$car->is_approved),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Car $car) {
                        $car->is_approved = false;
                        $car->save();

                        ActivityLog::log(
                            'Rejected car listing',
                            'car_reject',
                            $car,
                            ['car_id' => $car->id, 'car_name' => $car->name]
                        );
                    })
                    ->visible(fn (Car $car) => $car->is_approved),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                ActivityLog::log(
                                    'Deleted car listing',
                                    'car_delete',
                                    null,
                                    ['car_id' => $record->id, 'car_name' => $record->name]
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
                                $record->is_approved = true;
                                $record->save();
                                ActivityLog::log(
                                    'Approved car listing',
                                    'car_approve',
                                    $record,
                                    ['car_id' => $record->id, 'car_name' => $record->name]
                                );
                            }
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_approved = false;
                                $record->save();
                                ActivityLog::log(
                                    'Rejected car listing',
                                    'car_reject',
                                    $record,
                                    ['car_id' => $record->id, 'car_name' => $record->name]
                                );
                            }
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_active = false;
                                $record->save();
                                ActivityLog::log(
                                    'Deactivated car listing',
                                    'car_deactivate',
                                    $record,
                                    ['car_id' => $record->id, 'car_name' => $record->name]
                                );
                            }
                        }),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_active = true;
                                $record->save();
                                ActivityLog::log(
                                    'Activated car listing',
                                    'car_activate',
                                    $record,
                                    ['car_id' => $record->id, 'car_name' => $record->name]
                                );
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('is_approved', false)->count() ? 'warning' : null;
    }
}
