<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        // Calculate monthly revenue
        $currentMonthStart = Carbon::now()->startOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $currentMonthRevenue = Transaction::where('status', 'paid')
            ->where('created_at', '>=', $currentMonthStart)
            ->sum('amount');
            
        $previousMonthRevenue = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount');
        
        $revenueChange = $previousMonthRevenue ? ($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue * 100 : 0;
        
        // Appointment stats
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $approvedAppointments = Appointment::where('status', 'approved')->count();
        
        // Car listing stats
        $totalCars = Car::count();
        $pendingApprovalCars = Car::where('is_approved', false)->count();
        $activeCars = Car::where('is_active', true)->where('is_approved', true)->count();
        
        // User stats
        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', $currentMonthStart)->count();
        
        return [
            Stat::make('Monthly Revenue', '$' . number_format($currentMonthRevenue, 2))
                ->description($revenueChange >= 0 ? $revenueChange . '% increase' : abs($revenueChange) . '% decrease')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart([
                    Transaction::where('status', 'paid')
                        ->whereBetween('created_at', [
                            Carbon::now()->subDays(7)->startOfDay(),
                            Carbon::now()->endOfDay(),
                        ])
                        ->get()
                        ->groupBy(fn ($transaction) => $transaction->created_at->format('Y-m-d'))
                        ->map(fn ($transactions) => $transactions->sum('amount'))
                        ->values()
                        ->toArray()
                ]),
                
            Stat::make('Appointments', $totalAppointments)
                ->description($pendingAppointments . ' pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary')
                ->chart([
                    $pendingAppointments,
                    $approvedAppointments,
                    $totalAppointments - $pendingAppointments - $approvedAppointments,
                ]),
                
            Stat::make('Car Listings', $totalCars)
                ->description($pendingApprovalCars . ' pending approval')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([
                    $activeCars,
                    $pendingApprovalCars,
                    $totalCars - $activeCars - $pendingApprovalCars,
                ]),
                
            Stat::make('Users', $totalUsers)
                ->description($newUsersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),
        ];
    }
}
