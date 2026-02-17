<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create the permission if it doesn't exist
        $permissionName = 'access_login_logs';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);

        // Find the Admin role and give permission
        $role = Role::where('name', 'Admin')->first();
        if ($role) {
            $role->givePermissionTo($permission);
            // Also ensure they have units and currencies access as requested
            $role->givePermissionTo('access_units');
            $role->givePermissionTo('access_currencies');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We generally don't revoke permissions in down() because other roles might use them,
        // but for this specific task context we can leave it or revoke. 
        // Safer to leave it to avoid breaking other things.
    }
};
