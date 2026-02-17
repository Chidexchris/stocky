<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class AddAffiliateRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Clear spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create for both common guards
        Role::firstOrCreate(['name' => 'Affiliate', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Affiliate', 'guard_name' => 'api']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Typically we don't delete roles in down migrations if they might be in use
    }
}
