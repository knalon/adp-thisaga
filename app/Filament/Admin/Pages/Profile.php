<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $title = 'Profile';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.admin.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_photo' => $user->profile_photo,
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
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                FileUpload::make('profile_photo')
                    ->image()
                    ->directory('profile-photos')
                    ->visibility('public'),
                TextInput::make('current_password')
                    ->password()
                    ->dehydrated(false)
                    ->nullable(),
                TextInput::make('new_password')
                    ->password()
                    ->dehydrated(false)
                    ->nullable(),
                TextInput::make('new_password_confirmation')
                    ->password()
                    ->dehydrated(false)
                    ->nullable(),
            ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = User::find(Auth::id());

        if ($data['current_password']) {
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Current password is incorrect')
                    ->danger()
                    ->send();
                return;
            }

            if ($data['new_password']) {
                $user->password = Hash::make($data['new_password']);
            }
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];

        if (isset($data['profile_photo'])) {
            $user->profile_photo = $data['profile_photo'];
        }

        $user->save();

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}
