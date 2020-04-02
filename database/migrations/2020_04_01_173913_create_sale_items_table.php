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
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');

            $table->primary(['product_id','sale_id']);

            $table->string('status',100);
            $table->float('value');
            $table->float('discount');
            $table->float('creditUsed');
            $table->float('creditAdded');
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
