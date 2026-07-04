<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('quantity_in_stock', 10, 2)->default(0);
            $table->decimal('low_stock_threshold', 10, 2)->default(5);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->foreignId('cost_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->unique(['business_profile_id', 'sku'], 'uk_business_sku');
            $table->index(['business_profile_id', 'quantity_in_stock'], 'idx_low_stock');
        });
    }
    public function down(): void { Schema::dropIfExists('inventory'); }
};
