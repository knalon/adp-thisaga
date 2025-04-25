<?php

namespace App\Filament\User\Resources\MyAppointmentResource\Pages;

use App\Filament\User\Resources\MyAppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyAppointment extends CreateRecord
{
    protected static string $resource = MyAppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 