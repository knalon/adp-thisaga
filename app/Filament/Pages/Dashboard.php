<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AdminActivityLog;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            AdminActivityLog::class,
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationGroup::make('Dashboard')
                ->items([
                    NavigationItem::make('Overview')
                        ->icon('heroicon-o-home')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.dashboard'))
                        ->url(route('filament.pages.dashboard')),
                ]),

            NavigationGroup::make('Car Management')
                ->items([
                    NavigationItem::make('All Cars')
                        ->icon('heroicon-o-truck')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.cars.*'))
                        ->url(route('filament.resources.cars.index')),
                    
                    NavigationItem::make('Pending Approvals')
                        ->icon('heroicon-o-clock')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.cars.pending'))
                        ->url(route('filament.resources.cars.pending')),
                    
                    NavigationItem::make('Active Listings')
                        ->icon('heroicon-o-check-circle')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.cars.active'))
                        ->url(route('filament.resources.cars.active')),
                ]),

            NavigationGroup::make('User Management')
                ->items([
                    NavigationItem::make('Users')
                        ->icon('heroicon-o-users')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.users.*'))
                        ->url(route('filament.resources.users.index')),
                    
                    NavigationItem::make('Roles & Permissions')
                        ->icon('heroicon-o-key')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.roles.*'))
                        ->url(route('filament.resources.roles.index')),
                ]),

            NavigationGroup::make('Transactions')
                ->items([
                    NavigationItem::make('Pending Bids')
                        ->icon('heroicon-o-currency-dollar')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.bids.pending'))
                        ->url(route('filament.resources.bids.pending')),
                    
                    NavigationItem::make('Approved Bids')
                        ->icon('heroicon-o-check-circle')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.bids.approved'))
                        ->url(route('filament.resources.bids.approved')),
                    
                    NavigationItem::make('Completed Transactions')
                        ->icon('heroicon-o-shopping-cart')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.transactions.*'))
                        ->url(route('filament.resources.transactions.index')),
                ]),

            NavigationGroup::make('Appointments')
                ->items([
                    NavigationItem::make('Pending Appointments')
                        ->icon('heroicon-o-calendar')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.appointments.pending'))
                        ->url(route('filament.resources.appointments.pending')),
                    
                    NavigationItem::make('Approved Appointments')
                        ->icon('heroicon-o-check-circle')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.appointments.approved'))
                        ->url(route('filament.resources.appointments.approved')),
                ]),

            NavigationGroup::make('Settings')
                ->items([
                    NavigationItem::make('System Settings')
                        ->icon('heroicon-o-cog')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.settings'))
                        ->url(route('filament.pages.settings')),
                    
                    NavigationItem::make('Profile')
                        ->icon('heroicon-o-user')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.profile'))
                        ->url(route('filament.pages.profile')),
                ]),
        ];
    }
}
