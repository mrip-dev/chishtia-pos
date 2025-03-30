<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('Product Name');
            $table->string('image', 255)->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->string('sku', 40)->comment('Stock-keeping-unit');
            $table->unsignedInteger('alert_quantity')->nullable();
            $table->string('note', 255)->nullable();
            $table->unsignedInteger('total_sale')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
