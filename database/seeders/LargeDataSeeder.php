<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Salary;
use App\Models\Sales;
use App\Models\SalesPoint;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\UnitType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeDataSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET CONSTRAINTS ALL DEFERRED;');
        }
        
        echo "🚀 Memulai seeding 1000 data...\n\n";

        // Sales (50)
        echo "📊 Membuat 50 Sales...\n";
        $sales = $this->createSales(50);

        // Customers (200)
        echo "👥 Membuat 200 Customers...\n";
        $customers = $this->createCustomers(200, $sales);

        // Products (150)
        echo "📦 Membuat 150 Products...\n";
        $products = $this->createProducts(150);

        // Orders (300)
        echo "🛒 Membuat 300 Orders dengan Order Items...\n";
        $this->createOrders(300, $customers, $products);

        // Employees (50)
        echo "👔 Membuat 50 Employees...\n";
        $employees = $this->createEmployees(50);

        // Salaries (100)
        echo "💰 Membuat 100 Salaries...\n";
        $this->createSalaries(100, $employees);

        // Expenses (100)
        echo "💸 Membuat 100 Expenses...\n";
        $this->createExpenses(100);

        // Transactions (50)
        echo "💳 Membuat 50 Transactions...\n";
        $this->createTransactions(50);

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        echo "\n✅ Seeding selesai! Total 1000+ data berhasil dibuat.\n";
    }

    private function createSales(int $count): array
    {
        $sales = [];
        for ($i = 1; $i <= $count; $i++) {
            $sales[] = Sales::create([
                'name' => "Sales $i",
                'email' => "sales$i@test.com",
                'phone' => "08" . str_pad($i, 10, '0', STR_PAD_LEFT),
                'address' => "Alamat Sales $i",
                'status' => $i % 5 === 0 ? 'inactive' : 'active',
            ]);
        }
        return $sales;
    }

    private function createCustomers(int $count, array $sales): array
    {
        $customers = [];
        $statuses = ['new', 'repeat'];
        
        for ($i = 1; $i <= $count; $i++) {
            $customers[] = Customer::create([
                'name' => "Customer $i",
                'email' => "customer$i@test.com",
                'phone' => "08" . str_pad($i, 10, '0', STR_PAD_LEFT),
                'address' => "Alamat Customer $i",
                'sales_id' => $sales[array_rand($sales)]->id,
                'status_customer' => $statuses[array_rand($statuses)],
                'harga_komisi_standar' => rand(5000, 50000),
                'harga_komisi_ekstra' => rand(10000, 100000),
            ]);
        }
        return $customers;
    }

    private function createProducts(int $count): array
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $unitTypes = UnitType::all();
        $products = [];

        for ($i = 1; $i <= $count; $i++) {
            $buyingPrice = rand(10000, 100000);
            $sellingPrice = $buyingPrice + rand(5000, 50000);
            
            $products[] = Product::create([
                'name' => "Product $i",
                'product_number' => "PRD" . str_pad($i, 5, '0', STR_PAD_LEFT),
                'photo' => 'default.png',
                'status' => 'active',
                'category_id' => $categories->random()->id,
                'supplier_id' => $suppliers->random()->id,
                'unit_type_id' => $unitTypes->random()->id,
                'buying_price' => $buyingPrice,
                'selling_price' => $sellingPrice,
                'quantity' => rand(10, 1000),
                'bahan' => "Bahan $i",
                'gramatur' => rand(100, 500),
                'ukuran' => rand(10, 50) . 'x' . rand(10, 50),
                'ukuran_potongan_1' => rand(5, 25) . 'x' . rand(5, 25),
                'ukuran_plano_1' => rand(50, 100) . 'x' . rand(50, 100),
                'keterangan_tambahan' => "Keterangan Product $i",
            ]);
        }
        return $products;
    }

    private function createOrders(int $count, array $customers, array $products): void
    {
        $statuses = ['pending', 'processing', 'completed'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];
        $existingOrders = Order::count();

        for ($i = 1; $i <= $count; $i++) {
            $customer = $customers[array_rand($customers)];
            $subTotal = rand(100000, 5000000);
            $tax = $subTotal * 0.11;
            $discount = rand(0, $subTotal * 0.1);
            $total = $subTotal + $tax - $discount;
            $paid = rand(0, $total);
            $due = $total - $paid;

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => "ORD" . str_pad($existingOrders + $i, 6, '0', STR_PAD_LEFT),
                'tanggal_po' => now()->subDays(rand(1, 90)),
                'tanggal_kirim' => now()->addDays(rand(1, 30)),
                'sub_total' => $subTotal,
                'tax_total' => $tax,
                'discount_total' => $discount,
                'charge' => rand(0, 50000),
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
                'profit' => rand(10000, 500000),
                'loss' => 0,
                'status' => $statuses[array_rand($statuses)],
            ]);

            // Order Items (2-5 items per order)
            $itemCount = rand(2, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products[array_rand($products)];
                $qty = rand(1, 100);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_json' => json_encode([
                        'name' => $product->name,
                        'selling_price' => $product->selling_price,
                        'buying_price' => $product->buying_price,
                    ]),
                    'quantity' => $qty,
                ]);
            }

            // Sales Points
            if ($customer->sales_id) {
                $productTypes = ['box', 'kertas_nasi_padang'];
                SalesPoint::create([
                    'order_id' => $order->id,
                    'sales_id' => $customer->sales_id,
                    'product_type' => $productTypes[array_rand($productTypes)],
                    'jumlah_cetak' => rand(10, 500),
                    'points' => rand(1, 50),
                ]);
            }
        }
    }

    private function createEmployees(int $count): array
    {
        $employees = [];
        $designations = ['Manager', 'Staff', 'Supervisor', 'Operator', 'Admin'];
        $existingCount = Employee::count();
        
        for ($i = 1; $i <= $count; $i++) {
            $employees[] = Employee::create([
                'name' => "Employee " . ($existingCount + $i),
                'email' => "employee" . ($existingCount + $i) . "@test.com",
                'phone' => "08" . str_pad($i, 10, '0', STR_PAD_LEFT),
                'designation' => $designations[array_rand($designations)],
                'address' => "Alamat Employee $i",
                'salary' => rand(3000000, 10000000),
                'joining_date' => now()->subDays(rand(30, 365)),
            ]);
        }
        return $employees;
    }

    private function createSalaries(int $count, array $employees): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $employee = $employees[array_rand($employees)];
            Salary::create([
                'employee_id' => $employee->id,
                'amount' => $employee->salary,
                'salary_date' => now()->subMonths(rand(1, 12))->startOfMonth(),
            ]);
        }
    }

    private function createExpenses(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Expense::create([
                'name' => "Expense $i",
                'amount' => rand(100000, 5000000),
                'expense_date' => now()->subDays(rand(1, 90)),
                'description' => "Deskripsi Expense $i",
            ]);
        }
    }

    private function createTransactions(int $count): void
    {
        $orders = Order::where('due', '>', 0)->get();
        $existingCount = Transaction::count();
        $counter = 1;
        
        foreach ($orders->take($count) as $order) {
            $paymentAmount = rand(10000, min($order->due, 1000000));
            
            Transaction::create([
                'order_id' => $order->id,
                'transaction_number' => "TRX" . str_pad($existingCount + $counter, 6, '0', STR_PAD_LEFT),
                'amount' => $paymentAmount,
                'paid_through' => ['cash', 'transfer', 'credit'][rand(0, 2)],
            ]);

            $order->update([
                'paid' => $order->paid + $paymentAmount,
                'due' => $order->due - $paymentAmount,
            ]);
            
            $counter++;
        }
    }
}
