<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->enum('maintenance_type', ['scheduled', 'repair', 'calibration', 'inspection'])->default('scheduled');
            $table->text('description')->nullable();
            $table->string('performed_by')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->foreignId('cost_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->date('maintenance_date');
            $table->date('next_maintenance_date')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['next_maintenance_date', 'status'], 'idx_next_maintenance');
        });
    }
    public function down(): void { Schema::dropIfExists('equipment_maintenance'); }
};
