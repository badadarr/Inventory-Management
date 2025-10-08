<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  Please seed Products and Users first!');
            return;
        }

        $this->command->info('🔄 Creating Stock Movements...');

        $movements = [];

        // Create initial stock movements for each product
        foreach ($products as $product) {
            $currentBalance = 0;

            // Initial stock in (from purchase)
            $initialQty = rand(100, 500);
            $currentBalance += $initialQty;

            $movements[] = [
                'product_id' => $product->id,
                'reference_type' => 'purchase_order',
                'reference_id' => PurchaseOrder::inRandomOrder()->first()->id ?? null,
                'movement_type' => 'in',
                'quantity' => $initialQty,
                'balance_after' => $currentBalance,
                'notes' => 'Initial stock from purchase order',
                'created_by' => $users->where('role', 'warehouse')->first()->id ?? $users->first()->id,
                'created_at' => now()->subDays(rand(20, 40)),
                'updated_at' => now()->subDays(rand(20, 40)),
            ];

            // Add 2-4 random stock movements (in/out)
            $movementCount = rand(2, 4);
            for ($i = 0; $i < $movementCount; $i++) {
                $movementType = rand(0, 1) === 0 ? 'in' : 'out';
                $qty = rand(10, 100);

                if ($movementType === 'in') {
                    $currentBalance += $qty;
                    $refType = 'purchase_order';
                    $note = 'Stock in from purchase order';
                } else {
                    // Don't allow negative balance
                    if ($currentBalance - $qty < 0) {
                        continue;
                    }
                    $currentBalance -= $qty;
                    $refType = 'sales_order';
                    $note = 'Stock out for sales order';
                }

                $movements[] = [
                    'product_id' => $product->id,
                    'reference_type' => $refType,
                    'reference_id' => null,
                    'movement_type' => $movementType,
                    'quantity' => $qty,
                    'balance_after' => $currentBalance,
                    'notes' => $note,
                    'created_by' => $users->where('role', 'warehouse')->first()->id ?? $users->first()->id,
                    'created_at' => now()->subDays(rand(1, 19)),
                    'updated_at' => now()->subDays(rand(1, 19)),
                ];
            }

            // Update product quantity to match last balance
            $product->update(['quantity' => $currentBalance]);
        }

        // Bulk insert for better performance
        StockMovement::insert($movements);

        $this->command->info('✅ Created ' . count($movements) . ' stock movements for ' . $products->count() . ' products');
    }
}
