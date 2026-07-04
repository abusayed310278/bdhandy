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
        Schema::create('service_request_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('service_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');

            // Pricing
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('discount_type')->default('none'); // none, fixed, percent
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
            $table->string('tax_label')->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('adjustment_amount', 15, 2)->nullable(); // +/- manual adjustment
            $table->string('adjustment_note')->nullable();
            $table->decimal('total', 15, 2)->default(0);

            // Payment
            $table->string('payment_status')->default('draft'); // draft, pending, paid, partial, due
            $table->string('payment_method')->nullable();       // cash, card, online, cheque, bank_transfer, other
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('issued_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_invoices');
    }
};
