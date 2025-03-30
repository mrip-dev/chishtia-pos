<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('group', 40)->nullable();
            $table->string('code', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
