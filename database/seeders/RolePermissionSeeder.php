<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view_any_user', 'view_user', 'create_user', 'edit_user', 'delete_user',
            'view_any_subject', 'view_subject', 'create_subject', 'edit_subject', 'delete_subject',
            'view_any_grade', 'view_grade', 'create_grade', 'edit_grade', 'delete_grade',
            'view_own_grade' // Add these
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        $adminRole->syncPermissions(Permission::all());
        $teacherRole->syncPermissions([
            'view_any_grade', 'view_grade', 'create_grade', 'edit_grade', 'delete_grade',
            'view_any_user', 'view_user', 'create_user', 'edit_user',
        ]);
        $studentRole->syncPermissions(['view_own_grade']);

        $users = [
            [
                'email' => 'admin@example.com',
                'name' => 'Admin User',
                'role' => 'admin',
            ],
            [
                'email' => 'ahmed@student.com',
                'name' => 'Ahmed Student',
                'role' => 'student',
            ],
            [
                'email' => 'ahmedin@teacher.com',
                'name' => 'Ahmedin Teacher',
                'role' => 'teacher',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'), // Same password for all
                    'role_id' => Role::where('name', $userData['role'])->first()->id,
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}