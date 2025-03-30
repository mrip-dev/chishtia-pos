<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('title', 255)->nullable();
            $table->boolean('is_read')->default(0);
            $table->text('click_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_notifications');
    }
}
