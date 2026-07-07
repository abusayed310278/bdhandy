<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dateTime('next_billing_at')->nullable()->after('end_date');
            $table->dateTime('notified_3_day_at')->nullable()->after('next_billing_at');
            $table->dateTime('notified_6_hour_at')->nullable()->after('notified_3_day_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['next_billing_at', 'notified_3_day_at', 'notified_6_hour_at']);
        });
    }
};
