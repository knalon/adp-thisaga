<?php

namespace App\Filament\Pages;

use App\Models\Car;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyCars extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'My Cars';
    protected static ?string $title = 'My Cars';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.my-cars';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Car::query()
                    ->where('user_id', auth()->id())
            )
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
                Tables\Actions\EditAction::make()
                    ->url(fn (Car $record): string => route('filament.resources.cars.edit', $record)),
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
} 