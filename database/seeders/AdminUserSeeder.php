<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Ensure other roles exist
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'moderator']);
        Role::firstOrCreate(['name' => 'support']);
        Role::firstOrCreate(['name' => 'freelancer']);
        Role::firstOrCreate(['name' => 'business']);
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'affiliate']);

        // Create Admin User
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'System Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('11111111'),
                'user_code' => '000001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            $admin->update([
                'name' => 'System Admin',
                'password' => Hash::make('11111111'),
                'status' => 'active',
            ]);
        }

        // Assign Role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Admin account ready: admin@admin.com / 11111111');
    }
}
