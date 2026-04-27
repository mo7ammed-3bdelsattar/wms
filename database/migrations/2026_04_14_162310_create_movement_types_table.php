<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movement_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // purchase, sale, transfer, adjustment
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_types');
    }
};
