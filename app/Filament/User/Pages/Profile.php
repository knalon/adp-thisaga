<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.user.pages.profile';

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->profileData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'zip_code' => $user->zip_code,
        ];

        $this->form->fill($this->profileData);
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(['sm' => 2]),

                Section::make('Address Information')
                    ->schema([
                        TextInput::make('address')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(100),
                        TextInput::make('state')
                            ->maxLength(100),
                        TextInput::make('zip_code')
                            ->maxLength(20),
                    ])
                    ->columns(['sm' => 2]),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->currentPassword(),
                        Grid::make()
                            ->schema([
                                TextInput::make('new_password')
                                    ->label('New Password')
                                    ->password()
                                    ->required()
                                    ->rule(Password::default())
                                    ->different('current_password')
                                    ->same('new_password_confirmation'),
                                TextInput::make('new_password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required()
                                    ->dehydrated(false),
                            ]),
                    ]),
            ])
            ->statePath('passwordData');
    }

    protected function getFormStatePath(): string
    {
        return 'profileData';
    }

    protected function getProfileFormActions(): array
    {
        return [
            Action::make('saveProfile')
                ->label('Save Profile')
                ->submit('saveProfile'),
        ];
    }

    protected function getPasswordFormActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Change Password')
                ->submit('changePassword'),
        ];
    }

    public function saveProfile(): void
    {
        $data = $this->profileData;

        /** @var User $user */
        $user = Auth::user();
        $user->fill($data);
        $user->save();

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }

    public function changePassword(): void
    {
        $data = $this->passwordData;

        /** @var User $user */
        $user = Auth::user();
        $user->password = Hash::make($data['new_password']);
        $user->save();

        $this->passwordData = [];

        Notification::make()
            ->title('Password changed successfully')
            ->success()
            ->send();
    }
}
