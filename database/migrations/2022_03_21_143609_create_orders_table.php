<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->integer('distance');
            $table->dateTime('deadline');
            $table->unsignedBigInteger('assigned_pigeon')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->onDelete('CASCADE')
                ->on('customers');

            $table->foreign('assigned_pigeon')
                ->references('id')
                ->onDelete('CASCADE')
                ->on('pigeons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
