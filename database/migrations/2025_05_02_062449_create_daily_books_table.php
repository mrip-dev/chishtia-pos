<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_books', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('book_id')->nullable();
            $table->string('opening_balance')->nullable();
            $table->string('closing_balance')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_books');
    }
};
