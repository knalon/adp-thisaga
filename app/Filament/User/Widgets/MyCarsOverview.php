<?php

namespace App\Filament\User\Widgets;

use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyCarsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('My Cars', Car::where('user_id', $userId)->count())
                ->description('Total cars listed')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Active Listings', Car::where('user_id', $userId)->where('is_available', true)->count())
                ->description('Cars currently for sale')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),
            Stat::make('Sold Cars', Car::where('user_id', $userId)->where('is_available', false)->count())
                ->description('Cars sold')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
} 