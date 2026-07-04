<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->foreignId('team_role_id')->nullable()->constrained('team_roles')->nullOnDelete();
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20)->unique();
            $table->string('profile_photo')->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->string('nid_photo')->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('employee_code', 20)->unique();
            $table->string('designation', 100)->nullable();
            $table->date('joining_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'terminated'])->default('active');
            $table->enum('compensation_type', ['salary', 'commission', 'hybrid'])->default('salary');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_profile_id', 'status'], 'idx_business_status');
        });
    }
    public function down(): void { Schema::dropIfExists('team_members'); }
};
