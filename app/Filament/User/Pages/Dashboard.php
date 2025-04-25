<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\DashboardStatsWidget;
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
            // We'll add widgets here later
        ];
    }
}
