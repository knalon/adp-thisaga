<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Car Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->directory('car-images'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active Listing')
                    ->default(true),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('is_active', $value === 'active'),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn (Car $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (Car $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Car $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (Car $record): void {
                        $record->update(['is_active' => !$record->is_active]);
                    }),
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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
