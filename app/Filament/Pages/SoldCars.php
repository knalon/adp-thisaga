<?php

namespace App\Filament\Pages;

use App\Models\Car;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ActivityLog;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class SoldCars extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationLabel = 'Sold Cars';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.sold-cars';

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Let Filament's built-in authorization handle this
    }

    protected function getTableQuery(): Builder
    {
        return Car::query()
            ->where('is_sold', true)
            ->with(['user', 'transactions']);
    }

    protected function getTableColumns(): array
    {
        return [
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
            TextColumn::make('user.name')
                ->label('Seller')
                ->searchable()
                ->sortable(),
            TextColumn::make('transactions.user.name')
                ->label('Buyer')
                ->searchable()
                ->sortable(),
            TextColumn::make('sold_at')
                ->dateTime()
                ->sortable(),
            TextColumn::make('transactions.status')
                ->label('Payment Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'refunded' => 'info',
                    default => 'gray',
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('user')
                ->relationship('user', 'name')
                ->label('Seller')
                ->searchable()
                ->preload(),
            SelectFilter::make('buyer')
                ->relationship('transactions.user', 'name')
                ->label('Buyer')
                ->searchable()
                ->preload(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make()
                ->url(fn (Car $record): string => route('admin.cars.view', $record)),
            Tables\Actions\Action::make('generateInvoice')
                ->icon('heroicon-o-document-download')
                ->url(fn (Car $record): string => route('transactions.invoice', $record->transactions->first()))
                ->openUrlInNewTab()
                ->visible(fn (Car $record): bool => $record->transactions->isNotEmpty()),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
} 