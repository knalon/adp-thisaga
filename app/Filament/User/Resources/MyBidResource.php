<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyBidResource\Pages;
use App\Models\Bid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyBidResource extends Resource
{
    protected static ?string $model = Bid::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Bids & Offers';
    protected static ?string $navigationLabel = 'My Bids';
    protected static ?string $slug = 'my-bids';
    protected static ?int $navigationSort = 15;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bid Information')
                    ->schema([
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', fn ($query) => $query
                                ->select('id', 'make', 'model', 'registration_year')
                                ->where('is_active', true)
                                ->where('is_approved', true)
                                ->where('is_sold', false)
                                ->where('user_id', '!=', Auth::id()))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->make} {$record->model} ({$record->registration_year})")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('appointment_id')
                            ->relationship('appointment', 'id', fn ($query) => $query
                                ->where('user_id', Auth::id()))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Appointment #{$record->id} on " . $record->appointment_date->format('Y-m-d H:i'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->disabled(fn ($record) => $record !== null),
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
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null),
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
                Tables\Columns\TextColumn::make('car.make')
                    ->label('Make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.price')
                    ->label('Listed Price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('My Bid')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('highest_bid')
                    ->label('Highest Bid')
                    ->money('USD')
                    ->state(function (Bid $record): float {
                        $highestBid = $record->car->getHighestBid();
                        return $highestBid ? $highestBid->amount : 0;
                    }),
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
                    ->label('Bid Date')
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Bid $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('increaseBid')
                    ->label('Increase Bid')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->visible(fn (Bid $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('new_amount')
                            ->label('New Bid Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(fn (Bid $record) => $record->amount + 1)
                            ->default(fn (Bid $record) => $record->amount + 100),
                    ])
                    ->action(function (Bid $record, array $data) {
                        $record->update([
                            'amount' => $data['new_amount'],
                        ]);
                    }),
            ])
            ->bulkActions([])
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
            'index' => Pages\ListMyBids::route('/'),
            'create' => Pages\CreateMyBid::route('/create'),
            'edit' => Pages\EditMyBid::route('/{record}/edit'),
            'view' => Pages\ViewMyBid::route('/{record}'),
        ];
    }
}
