<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $users = User::all();

        if ($suppliers->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  Please seed Suppliers and Users first!');
            return;
        }

        $this->command->info('🔄 Creating Purchase Orders...');

        $purchaseOrders = [
            [
                'supplier_id' => $suppliers->random()->id,
                'order_number' => 'PO-20251007-0001',
                'order_date' => '2025-10-01',
                'total' => 15000000,
                'paid' => 15000000,
                'status' => 'received',
                'received_date' => '2025-10-05',
                'created_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'order_number' => 'PO-20251007-0002',
                'order_date' => '2025-10-03',
                'total' => 25000000,
                'paid' => 10000000,
                'status' => 'pending',
                'created_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'order_number' => 'PO-20251007-0003',
                'order_date' => '2025-10-05',
                'total' => 30000000,
                'paid' => 0,
                'status' => 'pending',
                'created_by' => $users->where('role', 'warehouse')->first()->id ?? $users->first()->id,
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'order_number' => 'PO-20251007-0004',
                'order_date' => '2025-09-20',
                'total' => 18000000,
                'paid' => 18000000,
                'status' => 'received',
                'received_date' => '2025-09-24',
                'created_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'order_number' => 'PO-20251007-0005',
                'order_date' => '2025-09-15',
                'total' => 12000000,
                'paid' => 12000000,
                'status' => 'cancelled',
                'created_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            ],
        ];

        foreach ($purchaseOrders as $poData) {
            PurchaseOrder::create($poData);
        }

        $this->command->info('✅ Created ' . count($purchaseOrders) . ' purchase orders');
    }
}
