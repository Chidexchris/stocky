<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GrantFullAccessToAllUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Clear Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Platform Permissions (to exclude)
        $platformPermissions = [
            'manage_businesses',
            'manage_plans',
            'access_platform_dashboard'
        ];

        // Ensure these exist so we don't error out if retrieving them
        foreach ($platformPermissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // 3. Get All Permissions Diff
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $businessPermissions = array_diff($allPermissions, $platformPermissions);

        // 4. Update 'Business Owner' Role
        $businessOwnerRole = Role::firstOrCreate(['name' => 'Business Owner']);
        $businessOwnerRole->syncPermissions($businessPermissions);
        $this->command->info("Updated 'Business Owner' role with " . count($businessPermissions) . " permissions.");

        // 5. Update 'Admin' Role (Just in case)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($businessPermissions);
        $this->command->info("Updated 'Admin' role with " . count($businessPermissions) . " permissions.");

        // 6. Assign Role to All Users (except Super Admin)
        $users = User::all();
        foreach ($users as $user) {
            if ($user->email === 'super.admin@test.com' || $user->hasRole('Super Admin')) {
                $this->command->info("Skipping Super Admin: {$user->email}");
                continue;
            }

            // Assign Business Owner role if not present
            if (!$user->hasRole('Business Owner')) {
                $user->assignRole('Business Owner');
                $this->command->info("Assigned 'Business Owner' to: {$user->email}");
            } else {
                $this->command->info("User already has 'Business Owner': {$user->email}");
            }
            
            // Also ensure they don't have conflicting roles if necessary, 
            // but adding Business Owner is additive so it's fine.
        }
    }
}
