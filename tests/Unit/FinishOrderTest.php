<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Pigeon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FinishOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function finished_order_status_is_correct()
    {
        $order = Order::factory()->onGoing()->create();

        $order->markAsFinished();

        $this->assertEquals($order->status, Order::STATUS_FINISHED);
    }

    /** @test */
    public function finished_orders_finished_time_will_be_now()
    {
        $now = now()->startOfDay();
        Carbon::setTestNow($now);
        $order = Order::factory()->onGoing()->create();

        $order->markAsFinished();

        $this->assertTrue($order->finished_time->equalTo($now));
    }

    /** @test */
    public function assigned_pigeons_previous_finished_order_time_will_be_now()
    {
        $now = now()->startOfDay();
        Carbon::setTestNow($now);
        $order = Order::factory()->onGoing()->create();

        $order->markAsFinished();

        $this->assertTrue($order->assignedPigeon->previous_finished_order_time->equalTo($now));
    }

    /** @test */
    public function assigned_pigeons_order_cycle_count_will_increment_by_one()
    {
        $now = now()->startOfDay();
        Carbon::setTestNow($now);
        $order = Order::factory()->onGoing()->create();
        $currentOrderCycleCount = $order->assignedPigeon->order_cycle_count;

        $order->markAsFinished();

        $this->assertEquals($order->assignedPigeon->order_cycle_count, $currentOrderCycleCount + 1);
    }

    /** @test */
    public function assigned_pigeons_order_cycle_count_will_reset_to_zero_if_reached_cycle_count_needed_to_take_a_rest()
    {
        $now = now()->startOfDay();
        Carbon::setTestNow($now);

        /** @var Pigeon */
        $pigeon = Pigeon::factory()->create([
            "order_cycle_count" => Order::CYCLE_COUNT_NEEDED_TO_TAKE_A_REST
        ]);
        $order = Order::factory()->onGoing()->create([
            "assigned_pigeon_id" => $pigeon
        ]);

        $order->markAsFinished();
        $pigeon->refresh();

        $this->assertEquals($pigeon->order_cycle_count, 0);
    }
}
