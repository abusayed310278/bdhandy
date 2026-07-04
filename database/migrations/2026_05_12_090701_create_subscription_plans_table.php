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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('duration_months');
            $table->decimal('price', 15, 2);
            $table->foreignId('currency_id')->constrained();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('lead_limit')->nullable();
            $table->integer('service_area_limit')->nullable();
            $table->integer('gallery_limit')->nullable();
            $table->integer('search_rank_weight')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified_badge_included')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
