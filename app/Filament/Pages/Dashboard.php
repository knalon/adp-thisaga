<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestActivities;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\SoldCarsOverview;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?int $navigationSort = 0;
    
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            SoldCarsOverview::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            SalesChart::class,
            LatestActivities::class,
        ];
    }
} 