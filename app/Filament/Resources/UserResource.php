<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('create_user');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('edit_user');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('delete_user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(table: User::class, ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role.name')->label('Role')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->relationship('role', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Sync roles after creating a new user
    protected function afterCreate(): void
    {
        $user = $this->record;
        \Log::info('UserResource::afterCreate', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);
        $role = \Spatie\Permission\Models\Role::find($user->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
            \Log::info('Role synced', ['role' => $role->name]);
        } else {
            \Log::warning('Role not found', ['role_id' => $user->role_id]);
        }
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        \Log::info('UserResource::afterSave', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);
        $role = \Spatie\Permission\Models\Role::find($user->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
            \Log::info('Role synced', ['role' => $role->name]);
        } else {
            \Log::warning('Role not found', ['role_id' => $user->role_id]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}