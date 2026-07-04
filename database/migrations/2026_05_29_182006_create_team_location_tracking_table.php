<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_location_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('accuracy_meters')->nullable();
            $table->decimal('heading', 5, 2)->nullable();
            $table->decimal('speed_kmh', 5, 2)->nullable();
            $table->unsignedTinyInteger('battery_level')->nullable();
            $table->boolean('is_moving')->default(false);
            $table->timestamp('location_time');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['team_member_id', 'created_at'], 'idx_member_recent');
            $table->index(['business_profile_id', 'created_at'], 'idx_business_active');
        });
    }
    public function down(): void { Schema::dropIfExists('team_location_tracking'); }
};
