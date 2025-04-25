<?php

namespace App\Filament\Resources\CarResource\RelationManagers;

use App\Models\ActivityLog;
use App\Models\Bid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BidsRelationManager extends RelationManager
{
    protected static string $relationship = 'bids';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Customer'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(1),
                Forms\Components\Select::make('appointment_id')
                    ->relationship('appointment', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Appointment #{$record->id} on " . $record->appointment_date->format('Y-m-d H:i'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
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
                Tables\Columns\TextColumn::make('appointment.appointment_date')
                    ->label('Appointment')
                    ->dateTime()
                    ->toggleable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['car_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
