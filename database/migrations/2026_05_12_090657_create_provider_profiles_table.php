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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider_type'); // freelancer, business
            $table->string('business_name')->nullable();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->text('description')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('experience_level')->nullable(); // beginner, intermediate, expert
            $table->json('languages')->nullable();
            $table->boolean('emergency_available')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('verification_status')->default('pending'); // pending, in_review, approved, rejected
            $table->boolean('is_featured')->default(false);
            $table->string('primary_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
