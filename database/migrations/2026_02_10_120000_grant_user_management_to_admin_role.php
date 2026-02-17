<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the permission exists (it should from seeder, but good to be safe)
        // Actually, permissions are usually created via seeder.
        // But we can check if the role exists and give permission.
        
        // We need to use the model, but Migrations runs in a specific context.
        // It's better to use Spatie's Role model directly.
        
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'Admin')->first();
        if ($role) {
            $role->givePermissionTo('access_user_management');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'Admin')->first();
        if ($role) {
            $role->revokePermissionTo('access_user_management');
        }
    }
};
