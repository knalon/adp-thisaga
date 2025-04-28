<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\DashboardStatsWidget;
use App\Filament\User\Widgets\CarSearchWidget;
use App\Filament\User\Widgets\MyRecentActivitiesWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.user.pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CarSearchWidget::class,
            MyRecentActivitiesWidget::class,
        ];
    }
}
