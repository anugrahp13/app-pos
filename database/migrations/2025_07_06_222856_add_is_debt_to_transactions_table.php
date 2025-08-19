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
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_debt')->default(false)->after('customer_id'); // sesuaikan posisi
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('paid')->after('is_debt');
            $table->date('due_date')->nullable()->after('status');
            $table->decimal('initial_payment', 15, 2)->default(0)->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_debt', 'status', 'due_date', 'initial_payment']);
        });
    }
};
