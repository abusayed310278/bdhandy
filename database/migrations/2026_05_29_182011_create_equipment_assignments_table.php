<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->foreignId('job_assignment_id')->nullable()->constrained('team_job_assignments')->nullOnDelete();
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->enum('returned_condition', ['good', 'damaged', 'lost'])->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('status', ['assigned', 'returned', 'lost'])->default('assigned');
            $table->timestamps();
            $table->index(['equipment_id', 'status'], 'idx_equipment_status');
            $table->index(['team_member_id', 'status'], 'idx_member_active');
        });
    }
    public function down(): void { Schema::dropIfExists('equipment_assignments'); }
};
