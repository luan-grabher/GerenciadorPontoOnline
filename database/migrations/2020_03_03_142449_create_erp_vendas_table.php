<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErpVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_vendas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('matricula');
            $table->bigInteger('tid');
            $table->string('aluno',200);
            $table->string('curso',200);
            $table->dateTime('dataRecebimento');
            $table->dateTime('dataPagamento');
            $table->float('valorTotal');
            $table->float("valorCursoSD");
            $table->float("valorCursoCD");
            $table->string("metodoPagamento");
            $table->integer("qxmat");
            $table->integer("soma");
            $table->integer("amais");
            $table->integer("credAluno");
            $table->integer("pgamenos");
            $table->string("cupom");
            $table->string("afiliados");
            $table->float("valorAfiliados");
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
        Schema::dropIfExists('erp_vendas');
    }
}
