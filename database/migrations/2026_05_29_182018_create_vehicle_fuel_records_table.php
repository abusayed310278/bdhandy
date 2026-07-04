<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_fuel_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('team_member_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->date('fuel_date');
            $table->decimal('liters_filled', 6, 2);
            $table->decimal('cost_per_liter', 8, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->foreignId('cost_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('odometer_reading', 10, 2)->nullable();
            $table->string('station_name')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'fuel_date'], 'idx_vehicle_date');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicle_fuel_records'); }
};
