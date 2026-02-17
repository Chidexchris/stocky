<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SaasTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Plans
        $basicPlan = Plan::firstOrCreate(['name' => 'Basic'], [
            'description' => 'For small businesses',
            'price' => 2900,
            'limit_users' => 2,
            'limit_stores' => 1,
        ]);

        $premiumPlan = Plan::firstOrCreate(['name' => 'Premium'], [
            'description' => 'For growing businesses',
            'price' => 9900,
            'limit_users' => 10,
            'limit_stores' => 5,
        ]);

        // 2. Create Businesses
        $businessA = Business::firstOrCreate(['email' => 'admin@businessa.com'], [
            'name' => 'Business A Corp',
            'plan_id' => $basicPlan->id,
            'is_active' => true,
        ]);

        $businessB = Business::firstOrCreate(['email' => 'admin@businessb.com'], [
            'name' => 'Business B Ltd',
            'plan_id' => $premiumPlan->id,
            'is_active' => true,
        ]);

        // 3. Create Users for Business A
        $userA = User::firstOrCreate(['email' => 'owner@businessa.com'], [
            'name' => 'A Owner',
            'password' => Hash::make('password'),
            'business_id' => $businessA->id,
            'is_active' => 1,
        ]);
        if (!$userA->hasRole('Business Owner')) {
            $userA->assignRole('Business Owner');
        }

        // 4. Create Store for Business A
        Store::firstOrCreate(['name' => 'Store A1', 'business_id' => $businessA->id]);

        // 5. Create Users for Business B
        $userB = User::firstOrCreate(['email' => 'owner@businessb.com'], [
            'name' => 'B Owner',
            'password' => Hash::make('password'),
            'business_id' => $businessB->id,
            'is_active' => 1,
        ]);
        if (!$userB->hasRole('Business Owner')) {
            $userB->assignRole('Business Owner');
        }
    }
}
