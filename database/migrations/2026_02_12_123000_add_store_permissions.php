<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'access_stores',
            'create_stores',
            'edit_stores',
            'delete_stores',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Grant all store permissions to Admin role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        // Grant all store permissions to Business Owner role
        $businessOwner = Role::where('name', 'Business Owner')->first();
        if ($businessOwner) {
            $businessOwner->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        $permissions = [
            'access_stores',
            'create_stores',
            'edit_stores',
            'delete_stores',
        ];

        foreach ($permissions as $perm) {
            $p = Permission::where('name', $perm)->first();
            if ($p) {
                $p->delete();
            }
        }
    }
};
