<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->enum('assignment_type', ['primary', 'assistant', 'supervisor'])->default('primary');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('scheduled_start_time')->nullable();
            $table->timestamp('scheduled_end_time')->nullable();
            $table->enum('status', ['assigned', 'accepted', 'en_route', 'arrived', 'in_progress', 'paused', 'completed', 'rejected', 'reassigned'])->default('assigned');
            $table->unsignedInteger('travel_time_minutes')->nullable();
            $table->unsignedInteger('actual_travel_time_minutes')->nullable();
            $table->unsignedInteger('work_duration_minutes')->nullable();
            $table->decimal('distance_traveled_km', 8, 2)->nullable();
            $table->timestamp('arrived_at_location')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('customer_rating')->nullable();
            $table->text('customer_feedback')->nullable();
            $table->decimal('commission_earned', 10, 2)->nullable();
            $table->foreignId('commission_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->timestamps();
            $table->unique(['service_request_id', 'assignment_type'], 'uk_request_assignment_type');
            $table->index(['team_member_id', 'status'], 'idx_team_member_status');
            $table->index('scheduled_start_time', 'idx_scheduled');
        });
    }
    public function down(): void { Schema::dropIfExists('team_job_assignments'); }
};
