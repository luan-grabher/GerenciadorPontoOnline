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
            $table->bigInteger('curso_id');
            $table->string('aluno',200);
            $table->text('curso');
            $table->dateTime('dataRecebimento');
            $table->dateTime('dataPagamento');
            $table->bigInteger('valorTotal');
            $table->bigInteger("valorCursoSD");
            $table->bigInteger("valorCursoCD");
            $table->string("metodoPagamento");
            $table->bigInteger("qxmat");
            $table->bigInteger("soma");
            $table->bigInteger("amais");
            $table->bigInteger("credUtilizado");
            $table->bigInteger("credAluno");
            $table->bigInteger("pgamenos");
            $table->string("cupom");
            $table->string("afiliados");
            $table->bigInteger("valorAfiliados");
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
