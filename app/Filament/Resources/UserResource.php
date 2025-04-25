<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\RolesEnum;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('zip_code')
                            ->maxLength(20),
                    ])->columns(2),
                Forms\Components\Section::make('User Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_banned')
                            ->label('Banned')
                            ->default(false),
                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_banned')
                    ->label('Banned')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple(),
                Tables\Filters\Filter::make('is_active')
                    ->toggle(),
                Tables\Filters\Filter::make('is_banned')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (User $user) {
                        $user->is_active = false;
                        $user->save();

                        ActivityLog::log(
                            'Deactivated user',
                            'user_deactivate',
                            $user,
                            ['user_id' => $user->id, 'user_email' => $user->email]
                        );
                    })
                    ->visible(fn (User $user) => $user->is_active),
                Tables\Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $user) {
                        $user->is_active = true;
                        $user->save();

                        ActivityLog::log(
                            'Activated user',
                            'user_activate',
                            $user,
                            ['user_id' => $user->id, 'user_email' => $user->email]
                        );
                    })
                    ->visible(fn (User $user) => !$user->is_active),
                Tables\Actions\Action::make('ban')
                    ->icon('heroicon-o-ban')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for ban (optional)')
                            ->placeholder('Enter reason for the ban...')
                            ->maxLength(500),
                    ])
                    ->action(function (User $user, array $data) {
                        $reason = $data['reason'] ?? '';
                        $user->ban($reason);
                    })
                    ->visible(fn (User $user) => !$user->is_banned),
                Tables\Actions\Action::make('unban')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $user) {
                        $user->unban();
                    })
                    ->visible(fn (User $user) => $user->is_banned),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                ActivityLog::log(
                                    'Deleted user',
                                    'user_delete',
                                    null,
                                    ['user_id' => $record->id, 'user_email' => $record->email]
                                );
                            }
                            $records->each->delete();
                        }),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_active = true;
                                $record->save();
                                ActivityLog::log(
                                    'Activated user',
                                    'user_activate',
                                    $record,
                                    ['user_id' => $record->id, 'user_email' => $record->email]
                                );
                            }
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->is_active = false;
                                $record->save();
                                ActivityLog::log(
                                    'Deactivated user',
                                    'user_deactivate',
                                    $record,
                                    ['user_id' => $record->id, 'user_email' => $record->email]
                                );
                            }
                        }),
                    Tables\Actions\BulkAction::make('ban')
                        ->label('Ban Selected')
                        ->icon('heroicon-o-ban')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Reason for ban (optional)')
                                ->placeholder('Enter reason for the ban...')
                                ->maxLength(500),
                        ])
                        ->action(function ($records, array $data) {
                            $reason = $data['reason'] ?? '';
                            foreach ($records as $record) {
                                $record->ban($reason);
                            }
                        }),
                    Tables\Actions\BulkAction::make('unban')
                        ->label('Unban Selected')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->unban();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
