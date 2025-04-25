<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyTransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class MyTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationLabel = 'My Transactions';
    protected static ?string $slug = 'transactions';
    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'make')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('appointment_id')
                            ->relationship('appointment', 'appointment_date')
                            ->disabled(),
                        Forms\Components\TextInput::make('final_price')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car.make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('car.model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn (Transaction $record) => $record->status === 'paid')
                    ->action(function (Transaction $record) {
                        $html = Blade::render(
                            '
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Invoice #{{ $transaction->id }}</title>
                                <style>
                                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                                    .invoice-header { text-align: center; margin-bottom: 30px; }
                                    .invoice-details { margin-bottom: 20px; }
                                    .invoice-table { width: 100%; border-collapse: collapse; }
                                    .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                    .invoice-table th { background-color: #f2f2f2; }
                                    .invoice-footer { margin-top: 30px; text-align: center; font-size: 0.8em; }
                                </style>
                            </head>
                            <body>
                                <div class="invoice-header">
                                    <h1>INVOICE</h1>
                                    <h3>Transaction #{{ $transaction->id }}</h3>
                                    <p>Date: {{ $transaction->created_at->format("Y-m-d") }}</p>
                                </div>
                                
                                <div class="invoice-details">
                                    <p><strong>From:</strong> CarDealership Inc.</p>
                                    <p><strong>To:</strong> {{ $transaction->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $transaction->user->email }}</p>
                                    <p><strong>Transaction Ref:</strong> {{ $transaction->transaction_reference }}</p>
                                </div>
                                
                                <table class="invoice-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Car Purchase</td>
                                            <td>{{ $transaction->car->make }} {{ $transaction->car->model }} ({{ $transaction->car->registration_year }})</td>
                                            <td>${{ number_format($transaction->final_price, 2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                                            <td><strong>${{ number_format($transaction->final_price, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <div class="invoice-footer">
                                    <p>Thank you for your business!</p>
                                </div>
                            </body>
                            </html>
                            ',
                            ['transaction' => $record]
                        );

                        $pdf = Pdf::loadHTML($html);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "invoice-{$record->id}.pdf"
                        );
                    }),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Transaction $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Transaction $record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'transaction_reference' => $data['payment_reference'],
                        ]);
                        
                        if ($record->car) {
                            $record->car->update([
                                'is_active' => false,
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false),
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
            'index' => Pages\ListMyTransactions::route('/'),
            'view' => Pages\ViewMyTransaction::route('/{record}'),
        ];
    }
} 