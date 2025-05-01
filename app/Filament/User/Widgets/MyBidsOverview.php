<?php

namespace App\Filament\User\Widgets;

use App\Enums\BidStatus;
use App\Models\Bid;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyBidsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Active Bids', Bid::where('user_id', $userId)
                ->where('status', BidStatus::PENDING)
                ->count())
                ->description('Bids awaiting response')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
                
            Stat::make('Accepted Bids', Bid::where('user_id', $userId)
                ->where('status', BidStatus::ACCEPTED)
                ->count())
                ->description('Bids accepted')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Bids', Bid::where('user_id', $userId)->count())
                ->description('All time bids placed')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
} 