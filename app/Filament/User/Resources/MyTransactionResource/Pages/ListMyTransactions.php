<?php

namespace App\Filament\User\Resources\MyTransactionResource\Pages;

use App\Filament\User\Resources\MyTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyTransactions extends ListRecords
{
    protected static string $resource = MyTransactionResource::class;
} 