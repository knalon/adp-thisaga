<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Activity;

class AdminActivityLog extends BaseWidget
{
    protected static ?string $heading = 'System Activity Log';
    protected int|string|array $columnSpan = 'full';

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Activity::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Activity')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('subject_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
} 