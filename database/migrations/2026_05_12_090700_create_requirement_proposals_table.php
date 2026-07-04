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
        Schema::create('requirement_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained('customer_requirements')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->decimal('proposed_price', 15, 2);
            $table->foreignId('currency_id')->constrained();
            $table->string('estimated_arrival_time')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected, withdrawn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_proposals');
    }
};
