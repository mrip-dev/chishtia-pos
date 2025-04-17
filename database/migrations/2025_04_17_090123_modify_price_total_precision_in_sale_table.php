<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->double('total_price', 28, 2)->change();
            $table->double('discount_amount', 28, 2)->change();
            $table->double('receivable_amount', 28, 2)->change();
            $table->double('received_amount', 28, 2)->change();
            $table->double('due_amount', 28, 2)->change();

        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->double('total_price', 28, 8)->change();
            $table->double('discount_amount', 28, 8)->change();
            $table->double('receivable_amount', 28, 8)->change();
            $table->double('received_amount', 28, 8)->change();
            $table->double('due_amount', 28, 8)->change();
        });
    }
};
