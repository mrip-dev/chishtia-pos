<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adjustment_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->unsignedTinyInteger('adjust_type')->comment('1 => Minus, 2 => Plus');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adjustment_details');
    }
};
