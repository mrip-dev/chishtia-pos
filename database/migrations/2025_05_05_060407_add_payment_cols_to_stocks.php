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
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('payment_status')->nullable();
            $table->string('bank_id')->nullable();
            $table->double('total_amount')->nullable();
            $table->double('due_amount')->nullable();
            $table->double('recieved_amount')->nullable();
            $table->double('bank_amount')->nullable();
            $table->double('cash_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {

            $table->dropColumn('payment_status');
            $table->dropColumn('bank_id');
            $table->dropColumn('total_amount');
            $table->dropColumn('due_amount');
            $table->dropColumn('recieved_amount');
            $table->dropColumn('bank_amount');
            $table->dropColumn('cash_amount');
        });
    }
};
