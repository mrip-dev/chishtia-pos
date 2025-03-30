<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('return_date');
            $table->decimal('total_price', 28, 8)->default(0.00000000);
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('payable_amount', 28, 8);
            $table->decimal('paid_amount', 28, 8)->default(0.00000000);
            $table->decimal('due_amount', 28, 8)->default(0.00000000);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_returns');
    }
};
