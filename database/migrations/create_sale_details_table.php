<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->double('price', 28, 8);
            $table->double('total', 28, 8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_details');
    }
}
