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
            $table->unsignedBigInteger('assigned_pigeon_id')->nullable();
            $table->dateTime('finished_time')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->onDelete('CASCADE')
                ->on('customers');

            $table->foreign('assigned_pigeon_id')
                ->references('id')
                ->onDelete('CASCADE')
                ->on('pigeons');

            $table->index(['status']);
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
