<?php

namespace Database\Seeders;

use App\Models\MovementType;
use App\Models\Reason;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Movement Types
        $types = [
            ['name' => 'Purchase', 'slug' => 'purchase'],
            ['name' => 'Sale', 'slug' => 'sale'],
            ['name' => 'Transfer', 'slug' => 'transfer'],
            ['name' => 'Adjustment In', 'slug' => 'adjustment-in'],
            ['name' => 'Adjustment Out', 'slug' => 'adjustment-out'],
        ];

        foreach ($types as $type) {
            MovementType::create($type);
        }

        // Seed Reasons
        $reasons = [
            ['name' => 'Stock Replenishment'],
            ['name' => 'Customer Order'],
            ['name' => 'Inventory Count'],
            ['name' => 'Damaged Goods'],
        ];

        foreach ($reasons as $reason) {
            Reason::create($reason);
        }

        // Seed Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@wms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Seed Seller
        User::create([
            'name' => 'System Seller',
            'email' => 'seller@wms.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
        ]);
    }
}
