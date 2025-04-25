<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Get sales data for the last 6 months
        $months = collect();
        $salesData = collect();

        // Get the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M Y'));
            
            $amount = Transaction::where('status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
                
            $salesData->push($amount);
        }

        // Reverse the collections to show oldest to newest
        $months = $months->reverse();
        $salesData = $salesData->reverse();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $salesData->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Blue with opacity
                    'borderColor' => 'rgb(59, 130, 246)', // Blue
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => '(value) => \'$\' + value',
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.3, // Smoother curve
                ],
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }
}
