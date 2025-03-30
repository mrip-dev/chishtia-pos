<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->double('price', 28, 8)->default(0.00000000);
            $table->double('total', 28, 8)->default(0.00000000);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_details');
    }
}
