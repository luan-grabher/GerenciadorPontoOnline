<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagarmeVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagarme_vendas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tid');
            $table->string('cliente',300)->default('');
            $table->string('status')->default('nenhum');
            $table->dateTime('dataPagamento');
            $table->string('metodoPagamento',100)->default('nenhum');
            $table->integer('parcelas')->default(0);
            $table->bigInteger('valor');
            $table->bigInteger('valorAutorizado');
            $table->bigInteger('valorPago');
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
        Schema::dropIfExists('pagarme_vendas');
    }
}
