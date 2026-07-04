<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('team_compensation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->decimal('base_salary_monthly', 12, 2)->nullable();
            $table->foreignId('salary_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->enum('commission_type', ['percentage', 'fixed_per_job', 'tiered'])->nullable();
            $table->decimal('commission_value', 10, 2)->nullable();
            $table->foreignId('commission_currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('weekly_guarantee_amount', 10, 2)->nullable();
            $table->enum('payment_frequency', ['weekly', 'biweekly', 'monthly'])->default('monthly');
            $table->date('next_payout_date')->nullable();
            $table->timestamps();
            $table->index(['effective_from', 'effective_to'], 'idx_effective');
        });
    }
    public function down(): void { Schema::dropIfExists('team_compensation'); }
};
