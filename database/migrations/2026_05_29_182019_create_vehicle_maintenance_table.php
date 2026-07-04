<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->enum('maintenance_type', ['oil_change', 'tyre', 'brake', 'engine', 'body', 'inspection', 'other']);
            $table->text('description')->nullable();
            $table->string('workshop_name')->nullable();
            $table->date('maintenance_date');
            $table->decimal('odometer_at_service', 10, 2)->nullable();
            $table->date('next_service_date')->nullable();
            $table->decimal('next_service_odometer_km', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->foreignId('cost_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->string('receipt_photo')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['next_service_date', 'status'], 'idx_next_service');
            $table->index(['vehicle_id', 'maintenance_date'], 'idx_vehicle_history');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicle_maintenance'); }
};
