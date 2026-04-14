<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movement_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('seller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('movement_type'); // e.g., 'IN', 'OUT', 'TRANSFER'
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('party_id')->nullable()->constrained('parties')->onDelete('set null');
            $table->foreignUuid('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_orders');
    }
};
