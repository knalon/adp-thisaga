<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Car;
use App\Models\Appointment;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        return [
            Stat::make('Active Listings', Car::where('user_id', $user->id)
                ->where('status', 'active')
                ->count())
                ->description('Your active car listings')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
                
            Stat::make('Pending Appointments', Appointment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count())
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
                
            Stat::make('Active Bids', Bid::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count())
                ->description('Your pending bids')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
                
            Stat::make('Sold Cars', Car::where('user_id', $user->id)
                ->where('status', 'sold')
                ->count())
                ->description('Successfully sold')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
