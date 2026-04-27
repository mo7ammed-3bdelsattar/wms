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
            $table->foreignUuid('seller_id')->nullable()->constrained('partners')->onDelete('set null');
            $table->foreignUuid('buyer_id')->nullable()->constrained('partners')->onDelete('set null');
            $table->foreignUuid('supplier_id')->nullable()->constrained('partners')->onDelete('set null');
            $table->foreignUuid('movement_type_id')->constrained('movement_types');
            $table->foreignUuid('reason_id')->nullable()->constrained('reasons');
            $table->text('notes')->nullable();
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
