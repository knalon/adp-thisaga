<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AdminActivityLog;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverview;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?int $navigationSort = -2;

    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        $widgets = [
            AdminStatsOverview::class,
        ];

        if (Auth::user() && Auth::user()->role === 'admin') {
            $widgets[] = AdminStatsOverview::class;
        } else {
            $widgets[] = UserStatsOverview::class;
        }

        return $widgets;
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

    public static function shouldRegister(): bool
    {
        return true;
    }
}

class AdminStatsOverview extends BaseStatsOverview
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total number of registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Cars', Car::count())
                ->description('Total number of car listings')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
            Stat::make('Pending Appointments', Appointment::where('status', 'pending')->count())
                ->description('Appointments waiting for approval')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}

class UserStatsOverview extends BaseStatsOverview
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('My Cars', Car::where('user_id', $userId)->count())
                ->description('Your active car listings')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('My Appointments', Appointment::where('user_id', $userId)->count())
                ->description('Your test drive appointments')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('Pending Bids', Appointment::where('user_id', $userId)->where('status', 'pending')->count())
                ->description('Your pending bids')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
