<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loadings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token',100)->unique();
            $table->string('name',200)->default("Carregando...");
            $table->string('stage_name',200)->default('Estágio');
            $table->integer("stage_max")->default(1);
            $table->integer('stage_min')->default(0);
            $table->integer('stage_val')->default(0);
            $table->string('action_name',200)->default('Ação');
            $table->integer('action_max')->default(1);
            $table->integer('action_min')->default(0);
            $table->integer('action_val')->default(0);
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
        Schema::dropIfExists('loadings');
    }
}
