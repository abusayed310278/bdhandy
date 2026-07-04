<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->enum('vehicle_type', ['bike', 'car', 'van', 'truck', 'other']);
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('plate_number', 30)->unique();
            $table->string('vin', 50)->nullable();
            $table->date('registration_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'cng', 'electric'])->default('petrol');
            $table->decimal('fuel_tank_capacity_liters', 6, 2)->nullable();
            $table->decimal('current_odometer_km', 10, 2)->default(0);
            $table->enum('status', ['available', 'assigned', 'in_maintenance', 'retired'])->default('available');
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['business_profile_id', 'status'], 'idx_business_status');
            $table->index(['registration_expiry', 'insurance_expiry'], 'idx_expiry');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicles'); }
};
