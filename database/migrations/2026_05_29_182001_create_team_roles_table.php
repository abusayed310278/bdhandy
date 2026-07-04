<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->string('role_name', 100);
            $table->json('permissions');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['business_profile_id', 'role_name'], 'uk_business_role');
        });
    }
    public function down(): void { Schema::dropIfExists('team_roles'); }
};
