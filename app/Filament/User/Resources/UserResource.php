<?php

namespace App\Filament\User\Resources;

use Filament\Resources\Resource;
use App\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Account';

    public static function getNavigationItems(): array
    {
        return [
            \App\Filament\User\Pages\Dashboard::class,
            \App\Filament\User\Resources\MyListingResource::class,
            \App\Filament\User\Resources\MyBidResource::class,
            \App\Filament\User\Resources\MySoldCarsResource::class,
            \App\Filament\User\Resources\MyPurchasedCarsResource::class,
            \App\Filament\User\Resources\MyAppointmentResource::class,
            \App\Filament\User\Pages\Profile::class,
        ];
    }

    public static function getNavigationGroups(): array
    {
        return [
            'Account' => [
                'label' => 'Account',
                'icon' => 'heroicon-o-user',
                'items' => [
                    \App\Filament\User\Pages\Dashboard::class,
                    \App\Filament\User\Resources\MyListingResource::class,
                    \App\Filament\User\Resources\MyBidResource::class,
                    \App\Filament\User\Resources\MySoldCarsResource::class,
                    \App\Filament\User\Resources\MyPurchasedCarsResource::class,
                    \App\Filament\User\Resources\MyAppointmentResource::class,
                    \App\Filament\User\Pages\Profile::class,
                ],
            ],
        ];
    }
}
