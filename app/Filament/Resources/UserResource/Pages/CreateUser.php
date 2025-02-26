<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $role = \Spatie\Permission\Models\Role::find($user->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
            \Log::info('CreateUser::afterCreate - Role synced', ['email' => $user->email, 'role' => $role->name]);
        }
    }
}