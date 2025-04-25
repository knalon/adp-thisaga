<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => config('app.name'),
            'company_address' => '123 Main Street, New York, NY 10001',
            'company_phone' => '+1 (555) 123-4567',
            'company_email' => 'info@abccars.com',
            'company_logo' => null,
            'primary_color' => '#10b981',
            'secondary_color' => '#3b82f6',
            'site_description' => 'ABC Cars is a premier car dealership offering a wide range of new and used vehicles.',
            'footer_text' => '© ' . date('Y') . ' ABC Cars. All rights reserved.',
            'social_facebook' => 'https://facebook.com/abccars',
            'social_twitter' => 'https://twitter.com/abccars',
            'social_instagram' => 'https://instagram.com/abccars',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required(),
                        TextInput::make('company_address')
                            ->label('Address')
                            ->required(),
                        TextInput::make('company_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required(),
                        TextInput::make('company_email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                    ])->columns(2),
                    
                Section::make('Appearance')
                    ->schema([
                        FileUpload::make('company_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('logos')
                            ->columnSpanFull(),
                        ColorPicker::make('primary_color')
                            ->label('Primary Color'),
                        ColorPicker::make('secondary_color')
                            ->label('Secondary Color'),
                    ]),
                    
                Section::make('SEO & Content')
                    ->schema([
                        Textarea::make('site_description')
                            ->label('Site Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('footer_text')
                            ->label('Footer Text')
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Social Media')
                    ->schema([
                        TextInput::make('social_facebook')
                            ->label('Facebook URL')
                            ->url(),
                        TextInput::make('social_twitter')
                            ->label('Twitter URL')
                            ->url(),
                        TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->url(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        
        // In a real application, you would save these to a settings table
        // or configuration file
        
        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
