<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_preferences', function (Blueprint $table) {
            // Per-event-type channel overrides
            // Structure: { "service_request.submitted": { "sms": true, "email": true, "push": true }, ... }
            $table->json('event_preferences')->nullable()->after('marketing_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->dropColumn('event_preferences');
        });
    }
};
