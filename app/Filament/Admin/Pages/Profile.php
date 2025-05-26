<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $title = 'Profile';
    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('current_password')
                    ->password()
                    ->dehydrated(false)
                    ->nullable(),
                TextInput::make('new_password')
                    ->password()
                    ->dehydrated(false)
                    ->nullable()
                    ->minLength(8),
                TextInput::make('new_password_confirmation')
                    ->password()
                    ->dehydrated(false)
                    ->nullable()
                    ->minLength(8),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        if ($data['current_password'] && $data['new_password']) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Current password is incorrect')
                    ->danger()
                    ->send();
                return;
            }

            if ($data['new_password'] !== $data['new_password_confirmation']) {
                Notification::make()
                    ->title('New passwords do not match')
                    ->danger()
                    ->send();
                return;
            }

            $user->password = Hash::make($data['new_password']);
        }

        User::where('id', $user->id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Forms\Components\Actions\Action::make('save')
                ->label('Save')
                ->submit('submit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Account';
    }
}
