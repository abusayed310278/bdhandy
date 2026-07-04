<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::updateOrCreate(['name' => 'admin']);
        foreach (['super_admin', 'moderator', 'support', 'freelancer', 'business', 'customer', 'affiliate', 'team_member'] as $role) {
            Role::updateOrCreate(['name' => $role]);
        }

        // 2. Define Permission Groups
        $permissionGroups = [
            'Dashboard' => [
                'view dashboard',
                'view statistics',
            ],
            'Roles & Permissions' => [
                'view roles',
                'create roles',
                'edit roles',
                'delete roles',
            ],
            'User Management' => [
                'view users',
                'create users',
                'edit users',
                'delete users',
            ],
            'Country' => [
                'view countries',
                'create countries',
                'edit countries',
                'delete countries',
            ],
            'Division' => [
                'view divisions',
                'create divisions',
                'edit divisions',
                'delete divisions',
            ],
            'District' => [
                'view districts',
                'create districts',
                'edit districts',
                'delete districts',
            ],
            'Area' => [
                'view areas',
                'create areas',
                'edit areas',
                'delete areas',
            ],
            'Category' => [
                'view categories',
                'create categories',
                'edit categories',
                'delete categories',
            ],
            'Service' => [
                'view services',
                'create services',
                'edit services',
                'delete services',
            ],
            'Currency' => [
                'view currencies',
                'create currencies',
                'edit currencies',
                'delete currencies',
            ],
            'Document Type' => [
                'view document types',
                'create document types',
                'edit document types',
                'delete document types',
            ],
            'Subscription Plan' => [
                'view subscription plans',
                'create subscription plans',
                'edit subscription plans',
                'delete subscription plans',
            ],
            'Coupon' => [
                'view coupons',
                'create coupons',
                'edit coupons',
                'delete coupons',
            ],
            'Banner' => [
                'view banners',
                'create banners',
                'edit banners',
                'delete banners',
            ],
            'FAQ' => [
                'view faqs',
                'create faqs',
                'edit faqs',
                'delete faqs',
            ],
            'System Setting' => [
                'view settings',
                'edit settings',
            ],
            'Language' => [
                'view languages',
                'create languages',
                'edit languages',
                'delete languages',
            ],
        ];

        // 3. Create Permissions and Assign to Admin
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::updateOrCreate([
                    'name' => $permissionName,
                    'group_name' => $group,
                ]);
                $adminRole->givePermissionTo($permission);
            }
        }

        // 4. Assign Admin Role to the first user if exists
        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
