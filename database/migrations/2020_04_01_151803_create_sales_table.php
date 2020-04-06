<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('profit');
            $table->date('date');
            $table->date('paymentDate');
            $table->bigInteger('tid');
            $table->string('paymentMethod',100);
            $table->integer('installments');
            $table->boolean('canceled');
            $table->text('justificationCancellation');
            $table->float('creditUsed');

            $table->string('customer_cpf',11);
            $table->foreign('customer_cpf')->references('cpf')->on('customers')->onDelete('cascade');

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
        Schema::dropIfExists('sales');
    }
}
