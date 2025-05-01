<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Car;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Bid;
use App\Models\Transaction;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Active Cars', Car::where('is_available', true)->count())
                ->description('Cars available for sale')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Pending Appointments', Appointment::where('status', 'pending')->count())
                ->description('Appointments awaiting confirmation')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            Stat::make('Pending Bids', Bid::where('status', 'pending')->count())
                ->description('Bids awaiting review')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger'),
        ];
    }
}
