<?php

namespace App\Filament\User\Widgets;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyAppointmentsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        return [
            Stat::make('Total Appointments', Appointment::where('user_id', $user->id)->count())
                ->description('All your appointments')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
                
            Stat::make('Upcoming Appointments', Appointment::where('user_id', $user->id)
                ->where('appointment_date', '>', now())
                ->where('status', AppointmentStatus::SCHEDULED)
                ->count())
                ->description('Scheduled appointments')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Completed Appointments', Appointment::where('user_id', $user->id)
                ->where('status', AppointmentStatus::COMPLETED)
                ->count())
                ->description('Past appointments')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
} 