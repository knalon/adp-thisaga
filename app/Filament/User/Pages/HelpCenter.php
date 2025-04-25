<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class HelpCenter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $slug = 'help-center';
    protected static ?string $navigationLabel = 'Help Center';
    protected static ?string $title = 'Help Center';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 30;

    public function render(): View
    {
        return view('filament.user.pages.help-center');
    }
}