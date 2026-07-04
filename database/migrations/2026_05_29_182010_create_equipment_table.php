<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->foreignId('purchase_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->enum('condition', ['new', 'good', 'fair', 'needs_repair', 'retired'])->default('good');
            $table->enum('status', ['available', 'assigned', 'in_maintenance', 'lost', 'retired'])->default('available');
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->index(['business_profile_id', 'status'], 'idx_business_status');
        });
    }
    public function down(): void { Schema::dropIfExists('equipment'); }
};
