<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seed.
     */
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $email = env('SUPER_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        $role = Role::firstWhere('name', 'super-admin');

        if (! $role) {
            $this->command?->warn('The super-admin role is missing; run RoleAndPermissionSeeder first.');
            return;
        }

        $user = User::firstOrCreate([
            'email' => $email,
        ], [
            'name' => env('SUPER_ADMIN_NAME', 'Local Super Admin'),
            'password' => Hash::make($password),
        ]);

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->command?->info("Local super-admin seeded: {$email}");
    }
}
