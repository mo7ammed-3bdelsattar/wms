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
        $this->call([
            RolesAndPermissionsSeeder::class,
            WmsSimulationSeeder::class,
        ]);
    }
}
