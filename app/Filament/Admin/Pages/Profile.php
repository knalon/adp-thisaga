<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;


class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.admin.pages.profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('current_password')
                    ->password()
                    ->required()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('new_password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->different('current_password'),
                Forms\Components\TextInput::make('new_password_confirmation')
                    ->password()
                    ->required()
                    ->same('new_password'),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (!Hash::check($data['current_password'], auth()->user()->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();
            return;
        }

        auth()->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['new_password']),
        ]);

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action('save'),
        ];
    }
}
