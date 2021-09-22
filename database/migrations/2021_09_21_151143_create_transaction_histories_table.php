<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_type');
            $table->foreign('transaction_type')->references('id')->on('transaction_types');
            $table->unsignedBigInteger('user_id_from');
            $table->foreign('user_id_from')->references('id')->on('users');
            $table->unsignedBigInteger('user_id_to');
            $table->foreign('user_id_to')->references('id')->on('users');
            $table->dateTime('date');
            $table->decimal('amount', 15,2);
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('is_rollback')->default(0);
            $table->unsignedBigInteger('rollback_transaction_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_histories');
    }
}
