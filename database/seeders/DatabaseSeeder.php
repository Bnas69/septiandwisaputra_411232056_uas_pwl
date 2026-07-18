<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedProducts();
        $this->call(MerchantSeeder::class);
        $this->seedSales();
        $this->seedStockTransactions();
    }

    private function seedUsers(): void
    {
        $accounts = [
            ['name' => 'Developer', 'username' => 'developer', 'email' => 'developer@smartcatalog.com', 'password' => 'Developer#2026', 'role' => 'developer'],
            ['name' => 'Owner', 'username' => 'owner', 'email' => 'owner@smartcatalog.com', 'password' => 'Owner#2026', 'role' => 'owner'],
            ['name' => 'Pegawai', 'username' => 'pegawai', 'email' => 'pegawai@smartcatalog.com', 'password' => 'Pegawai#2026', 'role' => 'pegawai'],
            ['name' => 'User', 'username' => 'user', 'email' => 'user@smartcatalog.com', 'password' => 'User#2026', 'role' => 'user'],
        ];

        foreach ($accounts as $account) {
            User::create([
                'name' => $account['name'],
                'username' => $account['username'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
                'role' => $account['role'],
                'status' => 'active',
            ]);
        }
    }

    private function seedProducts(): void
    {
        $products = [
            ['product_code' => 'PRD-000001', 'product_name' => 'Coffee Latte', 'category' => 'Coffee', 'price' => 35000, 'stock' => 150, 'minimum_stock' => 30],
            ['product_code' => 'PRD-000002', 'product_name' => 'Cappuccino', 'category' => 'Coffee', 'price' => 38000, 'stock' => 120, 'minimum_stock' => 25],
            ['product_code' => 'PRD-000003', 'product_name' => 'Cold Brew', 'category' => 'Coffee', 'price' => 42000, 'stock' => 80, 'minimum_stock' => 20],
            ['product_code' => 'PRD-000004', 'product_name' => 'Matcha Latte', 'category' => 'Non-Coffee', 'price' => 40000, 'stock' => 100, 'minimum_stock' => 20],
            ['product_code' => 'PRD-000005', 'product_name' => 'Chocolate Classic', 'category' => 'Non-Coffee', 'price' => 32000, 'stock' => 90, 'minimum_stock' => 20],
            ['product_code' => 'PRD-000006', 'product_name' => 'Gula Aren', 'category' => 'Addon', 'price' => 5000, 'stock' => 12, 'minimum_stock' => 20],
            ['product_code' => 'PRD-000007', 'product_name' => 'Espresso Single', 'category' => 'Coffee', 'price' => 25000, 'stock' => 200, 'minimum_stock' => 30],
            ['product_code' => 'PRD-000008', 'product_name' => 'Americano', 'category' => 'Coffee', 'price' => 28000, 'stock' => 180, 'minimum_stock' => 25],
            ['product_code' => 'PRD-000009', 'product_name' => 'Taro Latte', 'category' => 'Non-Coffee', 'price' => 38000, 'stock' => 60, 'minimum_stock' => 15],
            ['product_code' => 'PRD-000010', 'product_name' => 'Croissant Butter', 'category' => 'Food', 'price' => 22000, 'stock' => 45, 'minimum_stock' => 10],
            ['product_code' => 'PRD-000011', 'product_name' => 'Tiramisu Slice', 'category' => 'Food', 'price' => 35000, 'stock' => 8, 'minimum_stock' => 10],
            ['product_code' => 'PRD-000012', 'product_name' => 'Cheesecake Classic', 'category' => 'Food', 'price' => 38000, 'stock' => 15, 'minimum_stock' => 10],
            ['product_code' => 'PRD-000013', 'product_name' => 'Milkshake Vanilla', 'category' => 'Non-Coffee', 'price' => 30000, 'stock' => 50, 'minimum_stock' => 15],
            ['product_code' => 'PRD-000014', 'product_name' => 'Milkshake Strawberry', 'category' => 'Non-Coffee', 'price' => 30000, 'stock' => 5, 'minimum_stock' => 15],
            ['product_code' => 'PRD-000015', 'product_name' => 'Mineral Water', 'category' => 'Beverage', 'price' => 8000, 'stock' => 200, 'minimum_stock' => 50],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }

    private function seedSales(): void
    {
        $merchants = ['MCH-001', 'MCH-002', 'MCH-003', 'MCH-004', 'MCH-005'];
        $allProducts = Product::all();

        for ($i = 0; $i < 150; $i++) {
            $product = $allProducts->random();
            $qty = rand(1, 10);
            $date = now()->subDays(rand(0, 90));
            $subtotal = $product->price * $qty;

            Sale::create([
                'transaction_number' => Sale::generateTransactionNumber(),
                'transaction_date' => $date,
                'merchant_code' => $merchants[array_rand($merchants)],
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $product->price,
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
                'payment_method' => ['cash', 'qris', 'transfer'][array_rand(['cash', 'qris', 'transfer'])],
                'payment_status' => 'paid',
            ]);
        }
    }

    private function seedStockTransactions(): void
    {
        $allProducts = Product::all();
        $types = ['in', 'out'];

        for ($i = 0; $i < 30; $i++) {
            $product = $allProducts->random();
            $date = now()->subDays(rand(0, 60));
            $type = $types[array_rand($types)];

            StockTransaction::create([
                'stock_code' => StockTransaction::generateStockCode(),
                'stock_date' => $date,
                'type' => $type,
                'product_id' => $product->id,
                'qty' => rand(10, 50),
            ]);
        }
    }
}
