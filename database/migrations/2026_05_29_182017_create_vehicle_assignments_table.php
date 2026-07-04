<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->decimal('odometer_at_assignment', 10, 2)->nullable();
            $table->decimal('odometer_at_return', 10, 2)->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['team_member_id', 'status'], 'idx_member_vehicle');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicle_assignments'); }
};
