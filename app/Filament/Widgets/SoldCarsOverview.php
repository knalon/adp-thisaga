<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SoldCarsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        $totalSold = Car::where('is_sold', true)->count();
        $soldThisMonth = Car::where('is_sold', true)
            ->whereMonth('sold_at', Carbon::now()->month)
            ->count();
        
        $totalRevenue = Transaction::where('status', 'paid')
            ->sum('amount');
        $thisMonthRevenue = Transaction::where('status', 'paid')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->sum('amount');
            
        $recentTransactions = Transaction::where('status', 'paid')
            ->count();
        $pendingPayments = Transaction::where('status', 'pending')
            ->count();
            
        return [
            Stat::make('Total Sold Cars', $totalSold)
                ->description('All time')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
                
            Stat::make('Sold This Month', $soldThisMonth)
                ->description('Cars sold this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('All time')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Revenue This Month', '$' . number_format($thisMonthRevenue, 2))
                ->description('From sales this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
                
            Stat::make('Completed Transactions', $recentTransactions)
                ->description('All transactions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}