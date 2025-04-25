<?php

namespace App\Filament\User\Widgets;

use App\Models\Appointment;
use App\Models\Car;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Count purchased cars (cars with paid transactions where user is the buyer)
        $purchasedCarsCount = Car::whereHas('transactions', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 'paid');
        })->count();

        // Count sold cars (cars owned by user with paid transactions)
        $soldCarsCount = Car::where('user_id', $user->id)
            ->whereHas('transactions', function ($query) {
                $query->where('status', 'paid');
            })->count();

        return [
            Stat::make('Active Listings', Car::where('user_id', $user->id)->where('is_active', true)->count())
                ->description('Cars currently listed for sale')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            
            Stat::make('Pending Appointments', Appointment::where('user_id', $user->id)->where('status', 'pending')->count())
                ->description('Waiting for confirmation')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            
            Stat::make('Total Transactions', Transaction::where('user_id', $user->id)->count())
                ->description(Transaction::where('user_id', $user->id)->where('status', 'paid')->count() . ' completed')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Purchased Cars', $purchasedCarsCount)
                ->description('Cars you have bought')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
                
            Stat::make('Sold Cars', $soldCarsCount)
                ->description('Cars you have sold')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
} 