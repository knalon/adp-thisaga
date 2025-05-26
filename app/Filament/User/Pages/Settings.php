<?php

namespace App\Filament\User\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Auth;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.user.pages.settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Settings';

    protected static ?string $slug = 'settings';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
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
                    ->required(fn ($livewire) => $livewire->data['new_password'] !== null)
                    ->currentPassword(),
                TextInput::make('new_password')
                    ->password()
                    ->dehydrated(false)
                    ->minLength(8)
                    ->same('new_password_confirmation'),
                TextInput::make('new_password_confirmation')
                    ->password()
                    ->dehydrated(false)
                    ->minLength(8),
                FileUpload::make('avatar')
                    ->image()
                    ->directory('avatars')
                    ->visibility('public'),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if ($data['new_password']) {
            $user->password = Hash::make($data['new_password']);
        }

        if ($data['avatar']) {
            $user->avatar = $data['avatar'];
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        $this->notify('success', 'Settings saved successfully');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }

    public function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
