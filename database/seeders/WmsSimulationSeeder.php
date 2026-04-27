<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\{Category, Product, Partner, MovementType, Reason, User, MovementOrder, MovementOrderItem};
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class WmsSimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Master Data
            
            // Categories
            $categories = [
                Category::create(['name' => 'Electronics', 'description' => 'Various electronic devices']),
                Category::create(['name' => 'Home Appliances', 'description' => 'Household tools and appliances']),
                Category::create(['name' => 'Spare Parts', 'description' => 'Car and equipment spare parts']),
            ];

            // Products
            $products = [];
            for ($i = 1; $i <= 30; $i++) {
                $products[] = Product::create([
                    'name' => 'Product ' . $i,
                    'sku' => 'PRD-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'description' => 'Detailed description for Product ' . $i,
                    'category_id' => $categories[array_rand($categories)]->id,
                    'quantity' => 0, // Starts at 0
                ]);
            }

            // Partners
            $suppliers = [
                Partner::create(['type' => 'supplier', 'name' => 'First Supplier', 'contact_name' => 'Ahmed Supplier', 'contact_phone' => '0500000001', 'contact_email' => 'sup1@example.com', 'address' => 'Riyadh, Olaya St.']),
                Partner::create(['type' => 'supplier', 'name' => 'Tech Company', 'contact_name' => 'Khalid Tech', 'contact_phone' => '0500000002', 'contact_email' => 'sup2@example.com', 'address' => 'Jeddah, Palestine St.']),
                Partner::create(['type' => 'supplier', 'name' => 'Global Supplier', 'contact_name' => 'Omar Global', 'contact_phone' => '0500000003', 'contact_email' => 'sup3@example.com', 'address' => 'Dammam, Industrial Area']),
            ];

            $buyers = [
                Partner::create(['type' => 'buyer', 'name' => 'First Buyer', 'contact_name' => 'Ali Buyer']),
                Partner::create(['type' => 'buyer', 'name' => 'Second Buyer', 'contact_name' => 'Sami Buyer']),
            ];

            $sellers = [
                Partner::create(['type' => 'seller', 'name' => 'Main Seller', 'contact_name' => 'Fahad Seller']),
            ];

            // Movement Types
            $typeIn = MovementType::create(['name' => 'In', 'slug' => 'in']);
            $typeOut = MovementType::create(['name' => 'Out', 'slug' => 'out']);

            // Reasons
            $reasonPurchase = Reason::create(['name' => 'Purchase', 'description' => 'Purchase goods from supplier']);
            $reasonSales = Reason::create(['name' => 'Sales', 'description' => 'Sell goods to customer']);
            $reasonDamaged = Reason::create(['name' => 'Damaged', 'description' => 'Damaged goods']);

            // Users
            $superAdmin = User::create([
                'name' => 'System Admin',
                'email' => 'super@admin.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin'
            ]);
            
            $managerRole = Role::where('name', 'Warehouse Manager')->first();
            $manager = User::create([
                'name' => 'Manager',
                'email' => 'manager@wms.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
            if ($managerRole) {
                $manager->assignRole($managerRole);
            }

            // 2. Simulation Logic
            $startDate = Carbon::now()->subMonths(6);
            $endDate = Carbon::now();

            $currentDate = clone $startDate;

            $this->command->info("Simulating 6 months of WMS data from {$startDate->toDateString()} to {$endDate->toDateString()}...");

            while ($currentDate <= $endDate) {
                // 1. Random Purchases (In) - 30% chance per day
                if (rand(1, 100) <= 30) {
                    $this->createPurchaseOrder($currentDate, $suppliers, $products, $typeIn, $reasonPurchase, $superAdmin);
                }

                // 2. Random Sales (Out) - 60% chance
                if (rand(1, 100) <= 60) {
                    $this->createSalesOrder($currentDate, $products, $typeOut, $reasonSales, $buyers, $sellers);
                }

                $currentDate->addDay();
            }
            $this->command->info('Simulation completed successfully!');
        });
    }

    private function createPurchaseOrder($date, $suppliers, $products, $type, $reason, $user)
    {
        $supplier = $suppliers[array_rand($suppliers)];
        $order = MovementOrder::create([
            'supplier_id' => $supplier->id,
            'movement_type_id' => $type->id,
            'reason_id' => $reason->id,
            'notes' => 'Auto purchase order',
            'created_at' => clone $date,
            'updated_at' => clone $date,
        ]);

        $itemsCount = rand(3, 8);
        $selectedProducts = collect($products)->random($itemsCount);

        foreach ($selectedProducts as $product) {
            $qty = rand(50, 200);
            MovementOrderItem::create([
                'movement_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => rand(10, 1000),
                'created_at' => clone $date,
                'updated_at' => clone $date,
            ]);

            $this->updateStock($product->id, $qty);
        }
    }

    private function createSalesOrder($date, $products, $type, $reason, $buyers, $sellers)
    {
        $buyer = $buyers[array_rand($buyers)];
        $seller = $sellers[array_rand($sellers)];
        $order = MovementOrder::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'movement_type_id' => $type->id,
            'reason_id' => $reason->id,
            'notes' => 'Auto sales order',
            'created_at' => clone $date,
            'updated_at' => clone $date,
        ]);

        $itemsCount = rand(1, 4);
        $selectedProducts = collect($products)->random($itemsCount);

        foreach ($selectedProducts as $product) {
            // Need fresh product to get current stock because we update it in memory loop
            $currentProduct = Product::find($product->id);
            if ($currentProduct && $currentProduct->quantity > 0) {
                $qty = rand(1, min(5, $currentProduct->quantity));
                MovementOrderItem::create([
                    'movement_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => rand(10, 1000),
                    'created_at' => clone $date,
                    'updated_at' => clone $date,
                ]);

                $this->updateStock($product->id, -$qty);
            }
        }
    }

    private function updateStock($productId, $quantity)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->quantity += $quantity;
            // Not touching updated_at because we want it to look authentic? Let's just save.
            $product->save();
        }
    }
}
