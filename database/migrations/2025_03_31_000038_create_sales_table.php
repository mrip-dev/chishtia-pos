<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('invoice_no', 255);
            $table->unsignedBigInteger('warehouse_id');
            $table->date('sale_date');
            $table->decimal('total_price', 28, 8)->default(0.00000000);
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('receivable_amount', 28, 8)->default(0.00000000);
            $table->decimal('received_amount', 28, 8)->default(0.00000000);
            $table->decimal('due_amount', 28, 8)->default(0.00000000);
            $table->text('note')->nullable();
            $table->boolean('return_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
