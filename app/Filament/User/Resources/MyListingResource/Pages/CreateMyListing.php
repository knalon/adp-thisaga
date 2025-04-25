<?php

namespace App\Filament\User\Resources\MyListingResource\Pages;

use App\Filament\User\Resources\MyListingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyListing extends CreateRecord
{
    protected static string $resource = MyListingResource::class;

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