<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_member_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->enum('skill_level', ['junior', 'mid', 'senior'])->default('mid');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['team_member_id', 'service_id'], 'uk_member_service');
            $table->index(['service_id', 'business_profile_id'], 'idx_service_lookup');
        });
    }
    public function down(): void { Schema::dropIfExists('team_member_services'); }
};
