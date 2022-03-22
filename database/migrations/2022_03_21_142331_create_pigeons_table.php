<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePigeonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pigeons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('speed_per_hour');
            $table->integer('maximum_range');
            $table->integer('cost_per_distance');
            $table->integer('downtime');
            $table->integer('order_cycle_count')->default(0);
            $table->dateTime('previous_finished_order_time')->nullable();
            $table->timestamps();

            $table->index(['speed_per_hour']);
            $table->index(['order_cycle_count']);
            $table->index(['previous_finished_order_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pigeons');
    }
}
