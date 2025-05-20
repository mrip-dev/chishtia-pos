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
        Schema::table('suppliers', function (Blueprint $table) {
            // Drop unique constraint if it exists
            $table->dropUnique('suppliers_email_unique'); // Use index name
        });

        Schema::table('suppliers', function (Blueprint $table) {
            // Make columns nullable
            $table->string('email')->nullable()->change();
            $table->string('mobile')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Revert nullable changes
            $table->string('email')->nullable(false)->change();
            $table->string('mobile')->nullable(false)->change();

            // Re-add unique constraint
            $table->unique('email');
        });
    }
};
