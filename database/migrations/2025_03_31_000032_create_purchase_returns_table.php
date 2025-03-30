<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('supplier_id');
            $table->date('return_date');
            $table->decimal('total_price', 28, 8)->default(0.00000000);
            $table->decimal('discount_amount', 28, 8)->default(0.00000000);
            $table->decimal('receivable_amount', 28, 8)->default(0.00000000);
            $table->decimal('received_amount', 28, 8)->default(0.00000000);
            $table->decimal('due_amount', 28, 8)->default(0.00000000);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_returns');
    }
};
