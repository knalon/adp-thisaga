<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ActivityLog;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\FileUpload;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Transaction Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Customer'),
                        Select::make('appointment_id')
                            ->relationship('appointment', 'id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Appointment')
                            ->createOptionForm([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required(),
                                Forms\Components\Select::make('car_id')
                                    ->relationship('car', 'name')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('appointment_date')
                                    ->required(),
                            ]),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending Payment',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                Section::make('Payment Information')
                    ->schema([
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'debit_card' => 'Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'check' => 'Check',
                                'other' => 'Other',
                            ])
                            ->required(),
                        TextInput::make('transaction_id')
                            ->maxLength(255)
                            ->helperText('Reference number for the payment'),
                        DatePicker::make('payment_date')
                            ->label('Payment Date'),
                    ])
                    ->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        FileUpload::make('receipt')
                            ->directory('receipts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->helperText('Upload receipt PDF (if available)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions(self::getTableActions())
            ->bulkActions(self::getTableBulkActions());
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->toggleable(),
            TextColumn::make('user.name')
                ->label('Customer')
                ->searchable()
                ->sortable(),
            TextColumn::make('appointment.car.name')
                ->label('Car')
                ->searchable()
                ->sortable(),
            TextColumn::make('amount')
                ->money('USD')
                ->sortable(),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'refunded' => 'info',
                    default => 'gray',
                }),
            TextColumn::make('payment_method')
                ->searchable()
                ->sortable(),
            TextColumn::make('payment_date')
                ->date()
                ->sortable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending Payment',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ]),
            SelectFilter::make('payment_method')
                ->options([
                    'cash' => 'Cash',
                    'credit_card' => 'Credit Card',
                    'debit_card' => 'Debit Card',
                    'bank_transfer' => 'Bank Transfer',
                    'check' => 'Check',
                    'other' => 'Other',
                ]),
            Filter::make('payment_date')
                ->form([
                    Forms\Components\DatePicker::make('from'),
                    Forms\Components\DatePicker::make('until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                        )
                        ->when(
                            $data['until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                        );
                }),
        ];
    }

    private static function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('markAsPaid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Transaction $transaction) => $transaction->status === 'pending')
                ->form([
                    Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'credit_card' => 'Credit Card',
                            'debit_card' => 'Debit Card',
                            'bank_transfer' => 'Bank Transfer',
                            'check' => 'Check',
                            'other' => 'Other',
                        ])
                        ->required(),
                    TextInput::make('transaction_id')
                        ->maxLength(255)
                        ->helperText('Reference number for the payment'),
                    DatePicker::make('payment_date')
                        ->label('Payment Date')
                        ->default(now()),
                    Textarea::make('notes')
                        ->maxLength(500),
                ])
                ->action(function (Transaction $transaction, array $data) {
                    $transaction->status = 'paid';
                    $transaction->payment_method = $data['payment_method'];
                    $transaction->transaction_id = $data['transaction_id'];
                    $transaction->payment_date = $data['payment_date'];
                    $transaction->notes = $data['notes'];
                    $transaction->save();

                    // Update car status to sold
                    $car = $transaction->appointment->car;
                    $car->is_active = false;
                    $car->is_sold = true;
                    $car->sold_at = now();
                    $car->save();

                    ActivityLog::log(
                        'Marked transaction as paid',
                        'transaction_paid',
                        $transaction,
                        [
                            'transaction_id' => $transaction->id,
                            'amount' => $transaction->amount,
                            'user_id' => $transaction->user_id,
                        ]
                    );
                }),
            Tables\Actions\Action::make('updateShippingStatus')
                ->icon('heroicon-o-truck')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn (Transaction $transaction) => $transaction->status === 'paid')
                ->form([
                    Select::make('shipping_status')
                        ->options(Transaction::SHIPPING_STATUS)
                        ->required(),
                    DatePicker::make('shipping_date')
                        ->label('Shipping Date')
                        ->default(now()),
                    Textarea::make('shipping_notes')
                        ->maxLength(500),
                ])
                ->action(function (Transaction $transaction, array $data) {
                    $transaction->shipping_status = $data['shipping_status'];
                    $transaction->shipping_date = $data['shipping_date'];
                    $transaction->shipping_notes = $data['shipping_notes'] ?? null;
                    $transaction->save();

                    ActivityLog::log(
                        'Updated shipping status',
                        'transaction_shipping',
                        $transaction,
                        [
                            'transaction_id' => $transaction->id,
                            'shipping_status' => $transaction->shipping_status,
                        ]
                    );
                }),
            Tables\Actions\Action::make('generateInvoice')
                ->icon('heroicon-o-document-download')
                ->color('primary')
                ->visible(fn (Transaction $transaction) => $transaction->status === 'paid')
                ->url(fn (Transaction $transaction) => route('transaction.invoice', $transaction))
                ->openUrlInNewTab(),
        ];
    }

    private static function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            ActivityLog::log(
                                'Deleted transaction',
                                'transaction_delete',
                                null,
                                [
                                    'transaction_id' => $record->id,
                                    'amount' => $record->amount,
                                    'user_id' => $record->user_id,
                                ]
                            );
                        }
                        $records->each->delete();
                    }),
                Tables\Actions\BulkAction::make('markAllAsPaid')
                    ->label('Mark Selected as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->status === 'pending') {
                                $record->status = 'paid';
                                $record->payment_date = now();
                                $record->save();
                                ActivityLog::log(
                                    'Marked transaction as paid',
                                    'transaction_paid',
                                    $record,
                                    [
                                        'transaction_id' => $record->id,
                                        'amount' => $record->amount,
                                        'user_id' => $record->user_id,
                                    ]
                                );
                            }
                        }
                    }),
            ]),
        ];
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
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
