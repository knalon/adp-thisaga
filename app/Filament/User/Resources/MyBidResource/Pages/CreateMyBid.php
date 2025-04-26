<?php

namespace App\Filament\User\Resources\MyBidResource\Pages;

use App\Filament\User\Resources\MyBidResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyBid extends CreateRecord
{
    protected static string $resource = MyBidResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
