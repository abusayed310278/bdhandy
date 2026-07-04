<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->timestamp('clock_in_time');
            $table->decimal('clock_in_latitude', 10, 8)->nullable();
            $table->decimal('clock_in_longitude', 11, 8)->nullable();
            $table->string('clock_in_address', 500)->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->timestamp('clock_out_time')->nullable();
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->enum('status', ['clocked_in', 'on_break', 'clocked_out'])->default('clocked_in');
            $table->boolean('is_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status', 'idx_status');
        });
    }
    public function down(): void { Schema::dropIfExists('team_attendance'); }
};
