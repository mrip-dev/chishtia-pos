<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('name', 255);
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->unsignedInteger('cancelled_at')->nullable();
            $table->unsignedInteger('created_at');
            $table->unsignedInteger('finished_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_batches');
    }
}
