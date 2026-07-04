<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_schedule_waypoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_schedule_id')->constrained('team_daily_schedule')->cascadeOnDelete();
            $table->foreignId('job_assignment_id')->constrained('team_job_assignments')->cascadeOnDelete();
            $table->unsignedInteger('sequence_order');
            $table->unsignedInteger('estimated_travel_time_from_previous_minutes')->nullable();
            $table->decimal('estimated_distance_from_previous_km', 8, 2)->nullable();
            $table->timestamps();
            $table->unique(['daily_schedule_id', 'sequence_order'], 'uk_schedule_order');
        });
    }
    public function down(): void { Schema::dropIfExists('team_schedule_waypoints'); }
};
