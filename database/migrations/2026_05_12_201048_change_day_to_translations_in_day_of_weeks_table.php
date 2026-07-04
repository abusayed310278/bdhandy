<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('day_of_weeks', function (Blueprint $table) {
            $table->dropColumn('day');
            $table->json('translations')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_of_weeks', function (Blueprint $table) {
            $table->dropColumn('translations');
            $table->string('day')->after('id');
        });
    }
};
