<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_daily_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->date('schedule_date');
            $table->json('optimized_route')->nullable();
            $table->decimal('total_distance_km', 10, 2)->nullable();
            $table->unsignedInteger('estimated_total_duration_minutes')->nullable();
            $table->unsignedInteger('total_jobs_assigned')->default(0);
            $table->unsignedInteger('total_jobs_completed')->default(0);
            $table->decimal('total_earnings_day', 10, 2)->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
            $table->unique(['team_member_id', 'schedule_date'], 'uk_member_date');
            $table->index(['schedule_date', 'business_profile_id'], 'idx_date_business');
        });
    }
    public function down(): void { Schema::dropIfExists('team_daily_schedule'); }
};
