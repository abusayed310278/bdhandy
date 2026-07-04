<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_material_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_assignment_id')->constrained('team_job_assignments')->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('business_profile_id')->constrained('provider_profiles')->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventory')->cascadeOnDelete();
            $table->decimal('quantity_used', 10, 2);
            $table->decimal('unit_cost_at_time', 10, 2)->nullable();
            $table->foreignId('cost_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
            $table->index('job_assignment_id', 'idx_job');
            $table->index(['inventory_id', 'logged_at'], 'idx_inventory_usage');
        });
    }
    public function down(): void { Schema::dropIfExists('job_material_usage'); }
};
