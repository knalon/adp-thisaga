<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestActivities extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->with('user')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user_create', 'car_create', 'appointment_create', 'transaction_create' => 'success',
                        'user_update', 'car_update', 'appointment_update', 'transaction_update' => 'info',
                        'user_delete', 'car_delete', 'appointment_delete', 'transaction_delete' => 'danger',
                        'car_approve', 'appointment_approve', 'transaction_paid', 'user_activate' => 'success',
                        'car_reject', 'appointment_reject', 'user_deactivate' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return 'N/A';
                        $parts = explode('\\', $state);
                        return end($parts);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                // No actions needed for this widget
            ])
            ->defaultSort('created_at', 'desc');
    }
}
