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
        Schema::table('sales', function (Blueprint $table) {

            $table->string('service_type', 50)->default('takeaway')->after('status');
            $table->string('customer_address', 500)->nullable()->after('customer_phone');
            $table->string('table_no', 50)->nullable()->after('customer_address');
            $table->string('table_man', 255)->nullable()->after('table_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop the new columns in the reverse order of their creation
            $table->dropColumn([
                'service_type',
                'customer_address',
                'table_no',
                'table_man',
            ]);
        });
    }
};