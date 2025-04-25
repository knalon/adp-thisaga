<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SoldCars extends Page
{
    public static function shouldRegisterNavigation(): bool
    {
        return true; // Let Filament's built-in authorization handle this
    }
}