<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagarmeRecebimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagarme_recebimentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idOperacao');
            $table->bigInteger('idTransacao');
            $table->string('status',100);
            $table->string('metodoPagamento',100);
            $table->bigInteger('parcela');
            $table->dateTime('dataRecebimento');
            $table->dateTime('dataPagamento');
            $table->bigInteger('entrada');
            $table->bigInteger('saida');
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
        Schema::dropIfExists('pagarme_recebimentos');
    }
}
