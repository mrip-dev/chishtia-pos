<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->date('adjust_date')->nullable();
            $table->string('tracking_no', 40);
            $table->string('note', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adjustments');
    }
}
