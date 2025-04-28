<?php

namespace App\Filament\User\Widgets;

use App\Models\Car;
use App\Models\Appointment;
use App\Models\Bid;
use App\Models\Transaction;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MyRecentActivitiesWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.my-recent-activities-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = Auth::user();

        // Recent bids made by the user
        $recentBids = Bid::where('user_id', $user->id)
            ->with('car')
            ->latest()
            ->take(5)
            ->get();

        // Recent appointments scheduled by the user
        $recentAppointments = Appointment::where('user_id', $user->id)
            ->with('car')
            ->latest()
            ->take(5)
            ->get();

        // Recent transactions by the user
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('car')
            ->latest()
            ->take(5)
            ->get();

        // Combined activity feed
        $activities = collect();

        foreach ($recentBids as $bid) {
            $activities->push([
                'type' => 'bid',
                'icon' => 'heroicon-o-currency-dollar',
                'title' => 'You placed a bid of $' . number_format($bid->amount, 2),
                'description' => 'on ' . $bid->car->make . ' ' . $bid->car->model,
                'status' => $bid->status,
                'date' => $bid->created_at,
                'url' => route('filament.user.resources.my-bid-resource.view', ['record' => $bid->id]),
            ]);
        }

        foreach ($recentAppointments as $appointment) {
            $activities->push([
                'type' => 'appointment',
                'icon' => 'heroicon-o-calendar',
                'title' => 'You scheduled a test drive',
                'description' => 'for ' . $appointment->car->make . ' ' . $appointment->car->model,
                'status' => $appointment->status,
                'date' => $appointment->created_at,
                'appointment_date' => $appointment->appointment_date,
                'url' => route('filament.user.resources.my-appointment-resource.view', ['record' => $appointment->id]),
            ]);
        }

        foreach ($recentTransactions as $transaction) {
            $activities->push([
                'type' => 'transaction',
                'icon' => 'heroicon-o-banknotes',
                'title' => 'You ' . ($transaction->car->user_id == $user->id ? 'sold' : 'purchased') . ' a car',
                'description' => $transaction->car->make . ' ' . $transaction->car->model . ' for $' . number_format($transaction->amount, 2),
                'status' => $transaction->status,
                'date' => $transaction->created_at,
                'url' => route('filament.user.resources.my-transaction-resource.view', ['record' => $transaction->id]),
            ]);
        }

        // Sort activities by date
        $activities = $activities->sortByDesc('date')->values();

        return [
            'activities' => $activities,
        ];
    }
}
