<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('name', 40)->nullable();
            $table->string('email', 40)->unique()->nullable();
            $table->string('mobile', 40)->nullable();
            $table->string('username', 40)->unique()->nullable();
            $table->string('image', 255)->nullable();
            $table->string('password', 255);
            $table->boolean('status')->default(1)->comment('1 => Enable, 0 => Disabled');
            $table->string('remember_token', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
