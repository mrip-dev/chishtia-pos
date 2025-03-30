<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('invoice_no', 255);
            $table->unsignedBigInteger('warehouse_id');
            $table->date('purchase_date')->nullable();
            $table->decimal('total_price', 28, 8)->default(0.00000000);
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('payable_amount', 28, 8)->default(0.00000000);
            $table->decimal('paid_amount', 28, 8)->default(0.00000000);
            $table->decimal('due_amount', 28, 8)->default(0.00000000);
            $table->text('note')->nullable();
            $table->boolean('return_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
