<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidResource\Pages;
use App\Models\Bid;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BidResource extends Resource
{
    protected static ?string $model = Bid::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Bids & Offers';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bid Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Customer'),
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', fn ($query) => $query->select('id', 'make', 'model', 'registration_year'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->make} {$record->model} ({$record->registration_year})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('appointment_id')
                            ->relationship('appointment', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Appointment #{$record->id} on " . $record->appointment_date->format('Y-m-d H:i'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(1),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'outbid' => 'Outbid',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.make')
                    ->label('Make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        'outbid' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'outbid' => 'Outbid',
                    ]),
                Tables\Filters\SelectFilter::make('car')
                    ->relationship('car', 'make')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('accept')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Bid $bid) => $bid->status === 'pending')
                    ->action(function (Bid $bid) {
                        // Mark other bids as outbid
                        Bid::where('car_id', $bid->car_id)
                            ->where('id', '!=', $bid->id)
                            ->where('status', 'pending')
                            ->update(['status' => 'outbid']);

                        // Accept this bid
                        $bid->update(['status' => 'accepted']);

                        // Log the action
                        ActivityLog::log(
                            'Bid accepted',
                            'bid_accept',
                            $bid,
                            [
                                'bid_id' => $bid->id,
                                'car_id' => $bid->car_id,
                                'user_id' => $bid->user_id,
                                'amount' => $bid->amount
                            ]
                        );
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Bid $bid) => $bid->status === 'pending')
                    ->action(function (Bid $bid) {
                        $bid->update(['status' => 'rejected']);

                        // Log the action
                        ActivityLog::log(
                            'Bid rejected',
                            'bid_reject',
                            $bid,
                            [
                                'bid_id' => $bid->id,
                                'car_id' => $bid->car_id,
                                'user_id' => $bid->user_id,
                                'amount' => $bid->amount
                            ]
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('acceptBids')
                        ->label('Accept Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    // Mark other bids as outbid
                                    Bid::where('car_id', $record->car_id)
                                        ->where('id', '!=', $record->id)
                                        ->where('status', 'pending')
                                        ->update(['status' => 'outbid']);

                                    // Accept this bid
                                    $record->update(['status' => 'accepted']);

                                    // Log the action
                                    ActivityLog::log(
                                        'Bid accepted',
                                        'bid_accept',
                                        $record,
                                        [
                                            'bid_id' => $record->id,
                                            'car_id' => $record->car_id,
                                            'user_id' => $record->user_id,
                                            'amount' => $record->amount
                                        ]
                                    );
                                }
                            }
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('rejectBids')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'rejected']);

                                    // Log the action
                                    ActivityLog::log(
                                        'Bid rejected',
                                        'bid_reject',
                                        $record,
                                        [
                                            'bid_id' => $record->id,
                                            'car_id' => $record->car_id,
                                            'user_id' => $record->user_id,
                                            'amount' => $record->amount
                                        ]
                                    );
                                }
                            }
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBid::route('/create'),
            'edit' => Pages\EditBid::route('/{record}/edit'),
            'view' => Pages\ViewBid::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ? 'warning' : null;
    }
}
