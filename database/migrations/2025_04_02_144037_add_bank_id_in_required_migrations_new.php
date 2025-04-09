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
            $table->unsignedBigInteger('bank_id')->nullable()->after('customer_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('supplier_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('bank_id');
        });
    }
};
