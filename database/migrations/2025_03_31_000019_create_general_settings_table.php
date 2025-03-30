<?php
namespace Database\Migrations;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text', 40)->nullable()->comment('currency text');
            $table->string('cur_sym', 40)->nullable()->comment('currency symbol');
            $table->string('email_from', 40)->nullable();
            $table->string('email_from_name', 255)->nullable();
            $table->text('email_template')->nullable();
            $table->string('active_template', 255);
            $table->string('sms_template', 255)->nullable();
            $table->string('sms_from', 255)->nullable();
            $table->text('mail_config')->nullable()->comment('email configuration');
            $table->text('sms_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->boolean('en')->default(0)->comment('email notification, 0 - dont send, 1 - send');
            $table->boolean('sn')->default(0)->comment('sms notification, 0 - dont send, 1 - send');
            $table->boolean('system_customized')->default(0);
            $table->integer('paginate_number')->default(0);
            $table->tinyInteger('currency_format')->default(0)->comment('1=>Both, 2=>Text Only, 3=>Symbol Only');
            $table->string('available_version', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
};
