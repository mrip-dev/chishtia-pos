
<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_id');
            $table->string('transactable_type', 255);
            $table->unsignedBigInteger('transactable_id');
            $table->string('debit', 255)->nullable();
            $table->string('credit', 255)->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('payment_method', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index(['transactable_type', 'transactable_id'], 'bank_transactions_transactable_type_transactable_id_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_transactions');
    }
}
