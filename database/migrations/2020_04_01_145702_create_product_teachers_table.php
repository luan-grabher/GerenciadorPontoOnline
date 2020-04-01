<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_teachers', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('name',100)->primary();
            $table->float('percent');
            $table->integer('classes');
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
        Schema::dropIfExists('product_teachers');
    }
}
