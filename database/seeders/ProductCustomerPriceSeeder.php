<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCustomerPrice;
use Illuminate\Database\Seeder;

class ProductCustomerPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('⚠️  Please seed Products and Customers first!');
            return;
        }

        $this->command->info('🔄 Creating Product Customer Prices...');

        // Get some repeat customers for special pricing
        $repeatCustomers = $customers->where('status_customer', 'repeat')->take(5);

        $customPrices = [];

        foreach ($repeatCustomers as $customer) {
            // Give special prices to 3-5 random products per customer
            $customerProducts = $products->random(rand(3, 5));

            foreach ($customerProducts as $product) {
                // Custom price is 5-15% lower than standard selling price
                $discount = rand(5, 15) / 100;
                $customPrice = $product->selling_price * (1 - $discount);

                $customPrices[] = [
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'custom_price' => round($customPrice, 2),
                    'notes' => 'Harga spesial untuk customer repeat - diskon ' . ($discount * 100) . '%',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert for better performance
        ProductCustomerPrice::insert($customPrices);

        $this->command->info('✅ Created ' . count($customPrices) . ' custom prices for ' . $repeatCustomers->count() . ' customers');
    }
}
