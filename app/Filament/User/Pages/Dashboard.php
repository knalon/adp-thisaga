<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use App\Filament\User\Widgets\UserStatsOverview;
use App\Filament\User\Widgets\RecentActivity;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?int $navigationSort = -2;

    protected static string $view = 'filament.user.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
            RecentActivity::class,
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Overview')
                ->icon('heroicon-o-home')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.pages.dashboard'))
                ->url(route('filament.user.pages.dashboard')),

            NavigationItem::make('My Cars')
                ->icon('heroicon-o-truck')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.resources.cars.*'))
                ->url(route('filament.user.resources.cars.index')),

            NavigationItem::make('My Appointments')
                ->icon('heroicon-o-calendar')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.resources.appointments.*'))
                ->url(route('filament.user.resources.appointments.index')),

            NavigationItem::make('My Bids')
                ->icon('heroicon-o-currency-dollar')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.resources.bids.*'))
                ->url(route('filament.user.resources.bids.index')),

            NavigationItem::make('My Sold Cars')
                ->icon('heroicon-o-check-circle')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.resources.sold-cars.*'))
                ->url(route('filament.user.resources.sold-cars.index')),

            NavigationItem::make('My Purchased Cars')
                ->icon('heroicon-o-shopping-cart')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.resources.purchased-cars.*'))
                ->url(route('filament.user.resources.purchased-cars.index')),

            NavigationItem::make('Settings')
                ->icon('heroicon-o-cog')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.user.pages.settings'))
                ->url(route('filament.user.pages.settings')),
        ];
    }

    public static function shouldRegister(): bool
    {
        return true;
    }
}
