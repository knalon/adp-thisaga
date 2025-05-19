<?php

// app/Filament/User/Resources/BidResource/Pages/CreateBid.php

namespace App\Filament\User\Resources\BidResource\Pages;

use App\Filament\User\Resources\BidResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBid extends CreateRecord
{
    protected static string $resource = BidResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}