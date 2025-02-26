<?php
/// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Permission\Models\Role; // Add this import

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;

    protected $fillable = ['name', 'email', 'password', 'role_id'];

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'teacher', 'student']);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // Explicitly use Spatie's Role model
    }
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }
}