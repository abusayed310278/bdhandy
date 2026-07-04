<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate existing conversations & messages because they are request-based
        // and would violate foreign key constraints.
        Schema::disableForeignKeyConstraints();
        DB::table('messages')->truncate();
        DB::table('conversations')->truncate();
        Schema::enableForeignKeyConstraints();

        Schema::table('conversations', function (Blueprint $table) {
            // Drop related_request_id column if exists
            if (Schema::hasColumn('conversations', 'related_request_id')) {
                $table->dropColumn('related_request_id');
            }
            // Drop type column if exists
            if (Schema::hasColumn('conversations', 'type')) {
                $table->dropColumn('type');
            }

            // Drop customer_id and provider_id if they already exist from a failed partial run
            if (Schema::hasColumn('conversations', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('conversations', 'provider_id')) {
                $table->dropColumn('provider_id');
            }
        });

        // Separate step to cleanly add them now
        Schema::table('conversations', function (Blueprint $table) {
            // Add customer_id and provider_id
            $table->foreignId('customer_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->after('customer_id')->constrained('users')->onDelete('cascade');

            // Unique index to prevent duplicate conversations between the same customer and provider
            $table->unique(['customer_id', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'provider_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['customer_id', 'provider_id']);

            $table->string('type')->after('id');
            $table->unsignedBigInteger('related_request_id')->nullable()->after('type');
        });
    }
};
