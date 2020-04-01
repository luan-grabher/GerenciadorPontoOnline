<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->bigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            $table->primary(['product_id','sale_id']);

            $table->string('status',100);
            $table->float('value');
            $table->float('discount');
            $table->string('description',100);

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
        Schema::dropIfExists('sale_items');
    }
}
