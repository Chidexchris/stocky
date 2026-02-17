<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Sale\Entities\Sale;
use Modules\Purchase\Entities\Purchase;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;

class NinChartDataSeeder extends Seeder
{
    public function run()
    {
        $email = 'nin@gmail.com';
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->command->error("User $email not found.");
            return;
        }

        if (!$user->business_id) {
             // Fallback if still null, though tinker said 1
             $user->business_id = 1;
             $user->save();
             $this->command->info("Assigned business_id 1 to $email");
        }

        $business_id = $user->business_id;
        $store_id = $user->store_id ?? \App\Models\Store::where('business_id', $business_id)->first()->id;

        // Create Sales for last 7 days specifically for this user's context
        for ($i = 0; $i < 7; $i++) {
            Sale::create([
                'date' => Carbon::now()->subDays($i)->format('Y-m-d'),
                'reference' => 'NIN-SALE-' . $i,
                'customer_id' => 1, 
                'customer_name' => 'Nin Customer',
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => rand(2000, 6000) * 100,
                'paid_amount' => rand(2000, 6000) * 100,
                'due_amount' => 0,
                'status' => 'Completed',
                'payment_status' => 'Paid',
                'payment_method' => 'Cash',
                'note' => 'Seeded for Nin Chart',
                'business_id' => $business_id,
                'store_id' => $store_id
            ]);
        }

        $this->command->info("Created 7 days of sales data for business $business_id (User: $email).");
    }
}
