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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->string('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('urgency')->default('normal'); // normal, urgent, emergency
            $table->decimal('estimated_price', 15, 2)->nullable();
            $table->decimal('final_price', 15, 2)->nullable();
            $table->foreignId('currency_id')->constrained();
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('request_status')->default('pending'); // pending, accepted, in_progress, completed, disputed, cancelled
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
