<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sale\Entities\Sale;
use Modules\Purchase\Entities\Purchase;
use App\Models\User;
use Carbon\Carbon;

class ChartTestDataSeeder extends Seeder
{
    public function run()
    {
        // Find a business owner or admin user to associate data with
        $user = User::role('Business Owner')->first() ?? User::role('Admin')->first();
        
        if (!$user) {
            $this->command->error("No Business Owner or Admin user found to seed data.");
            return;
        }

        // Determine business/store context
        $business_id = $user->business_id ?? 1; // Fallback or logic to get valid ID
        // Assuming single store for simplicity or fetching first store
        $store_id = $user->store_id ?? \App\Models\Store::where('business_id', $business_id)->first()->id;

        // Create Sales for last 7 days
        for ($i = 0; $i < 7; $i++) {
            Sale::create([
                'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                'reference' => 'SALE-TEST-' . $i,
                'customer_id' => 1, // Ensure a customer exists or create one
                'customer_name' => 'Test Customer',
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => rand(1000, 5000) * 100, // Amount in cents
                'paid_amount' => rand(1000, 5000) * 100,
                'due_amount' => 0,
                'status' => 'Completed',
                'payment_status' => 'Paid',
                'payment_method' => 'Cash',
                'note' => 'Seeded for Chart Test',
                'business_id' => $business_id,
                'store_id' => $store_id
            ]);
        }

        // Create Purchases for last 7 days
        for ($i = 0; $i < 7; $i++) {
            Purchase::create([
                'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                'reference' => 'PUR-TEST-' . $i,
                'supplier_id' => 1, // Ensure a supplier exists
                'supplier_name' => 'Test Supplier',
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => rand(500, 3000) * 100,
                'paid_amount' => rand(500, 3000) * 100,
                'due_amount' => 0,
                'status' => 'Completed',
                'payment_status' => 'Paid',
                'payment_method' => 'Cash',
                'note' => 'Seeded for Chart Test',
                'business_id' => $business_id,
                'store_id' => $store_id
            ]);
        }
        
        $this->command->info("Created default sales and purchases for the last 7 days.");
    }
}
