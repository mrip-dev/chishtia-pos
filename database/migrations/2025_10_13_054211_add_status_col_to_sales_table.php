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
        Schema::table('sales', function (Blueprint $table) {
               $table->string('status')->default('pending')->nullable();
               $table->string('customer_name')->default('shop')->nullable();
               $table->string('customer_phone')->default(0000000)->nullable();
               $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('customer_name');
            $table->dropColumn('customer_phone');
            $table->dropColumn('user_id');
        });
    }
};
