<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SaasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Platform Permissions (Super Admin only)
        Permission::findOrCreate('manage_businesses', 'web');
        Permission::findOrCreate('manage_plans', 'web');
        Permission::findOrCreate('access_platform_dashboard', 'web');

        // Ensure Chart Permissions Exist
        $chartPermissions = [
            'show_total_stats',
            'show_month_overview',
            'show_weekly_sales_purchases',
            'show_monthly_cashflow',
            'show_notifications',
            'access_login_logs'
        ];
        foreach ($chartPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Business Owner Role
        $businessOwner = Role::firstOrCreate(['name' => 'Business Owner']);
        
        // Force refresh of permissions
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $platformPermissions = ['manage_businesses', 'manage_plans', 'access_platform_dashboard'];
        $businessPermissions = array_diff($allPermissions, $platformPermissions);
        
        $businessOwner->syncPermissions($businessPermissions);

        // Store Manager Role
        $storeManager = Role::firstOrCreate(['name' => 'Store Manager']);
        // Assign subset of permissions
        $storeManagerPermissions = [
            'access_products', 'create_products', 'show_products', 'edit_products',
            'access_sales', 'create_sales', 'show_sales', 'edit_sales',
            'access_customers', 'create_customers', 'show_customers'
        ];
        $storeManager->syncPermissions($storeManagerPermissions);

        // Store Staff Role
        $storeStaff = Role::firstOrCreate(['name' => 'Store Staff']);
        $storeStaffPermissions = [
            'show_products', 'create_sales', 'show_sales'
        ];
        $storeStaff->syncPermissions($storeStaffPermissions);
    }
}
