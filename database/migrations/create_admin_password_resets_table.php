<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPasswordResetsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email', 40)->nullable();
            $table->string('token', 40)->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_password_resets');
    }
}
