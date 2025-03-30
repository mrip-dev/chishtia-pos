<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtensionsTable extends Migration
{
    public function up()
    {
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('act', 40)->nullable();
            $table->string('name', 40)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->text('script')->nullable();
            $table->text('shortcode')->nullable()->comment('object');
            $table->text('support')->nullable()->comment('help section');
            $table->boolean('status')->default(1)->comment('1 => enable, 2 => disable');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extensions');
    }
}
