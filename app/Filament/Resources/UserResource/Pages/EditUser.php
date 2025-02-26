<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $user = $this->record;
        $role = \Spatie\Permission\Models\Role::find($user->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
            \Log::info('EditUser::afterSave - Role synced', ['email' => $user->email, 'role' => $role->name]);
        }
    }
}