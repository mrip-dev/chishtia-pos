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
        Schema::create('ware_house_detail_histories', function (Blueprint $table) {
            $table->id();
            $table->string('ware_house_id');
            $table->string('product_id');
            $table->string('supplier_id');
            $table->string('customer_id');
            $table->string('date');
            $table->string('stock_in');
            $table->string('stock_out');
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ware_house_detail_histories');
    }
};
