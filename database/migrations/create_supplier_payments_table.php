<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('purchase_return_id')->nullable();
            $table->decimal('amount', 28, 2)->unsigned()->default(0.00);
            $table->string('trx', 40);
            $table->string('remark', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_payments');
    }
}
