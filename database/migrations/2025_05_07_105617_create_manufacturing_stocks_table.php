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
        Schema::create('manufacturing_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('manufacturing_flow_id')->nullable();
            $table->string('product')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('date_of_stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_stocks');
    }
};
