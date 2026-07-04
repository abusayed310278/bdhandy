<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->unsignedInteger('team_member_limit')->default(1)->after('target');
            $table->json('team_features')->nullable()->after('team_member_limit');
        });
    }
    public function down(): void {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['team_member_limit', 'team_features']);
        });
    }
};
