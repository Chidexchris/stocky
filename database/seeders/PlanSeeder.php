<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for single stores and small shops starting out.',
                'audience' => 'Single Store / Small Shops',
                'is_popular' => false,
                'price' => 1500,         // $15/month in cents
                'price_annual' => 1200,  // $12/month billed annually in cents
                'limit_users' => 3,
                'limit_stores' => 2,
                'limit_storage' => 2,    // 2GB
                'limit_currencies' => 1,
                'features' => [
                    'Real-time Stock Tracking',
                    'Sales & PDF Invoices',
                    'Up to 3 Users',
                    '2GB Storage',
                    'Up to 2 Stores',
                    'Email Support',
                ],
            ],
            [
                'name' => 'Business',
                'description' => 'Great for supermarkets, pharmacies, and growing businesses.',
                'audience' => 'Supermarkets & Pharmacies',
                'is_popular' => true,
                'price' => 3900,         // $39/month in cents
                'price_annual' => 2900,  // $29/month billed annually in cents
                'limit_users' => 15,
                'limit_stores' => 10,
                'limit_storage' => 5,    // 5GB
                'limit_currencies' => 3,
                'features' => [
                    'Everything in Starter',
                    'Supplier Management',
                    'Customer Debt Tracking',
                    'Expiry Date Alerts',
                    'Up to 15 Users',
                    'Up to 10 Stores',
                    '5GB Storage',
                    'Priority WhatsApp Support',
                    'Login Logs Tracking',
                    'Barcode Printing',
                    'Expense Management',
                ],
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For warehouses and multi-store operations at scale.',
                'audience' => 'Warehouses & Multi-Store',
                'is_popular' => false,
                'price' => 7900,         // $79/month in cents
                'price_annual' => 5900,  // $59/month billed annually in cents
                'limit_users' => 50,
                'limit_stores' => 20,
                'limit_storage' => 10,   // 10GB
                'limit_currencies' => 5,
                'features' => [
                    'Everything in Business',
                    'Multi-location Sync',
                    'Inter-store Transfers',
                    'Advanced Analytics API',
                    'Dedicated Account Manager',
                    '10GB Storage',
                    'Up to 50 Users',
                    'Up to 20 Stores',
                    '24/7 Support',
                    'Advanced Reports',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        // Remove old plans that no longer exist
        Plan::whereNotIn('name', ['Starter', 'Business', 'Enterprise'])->delete();
    }
}

