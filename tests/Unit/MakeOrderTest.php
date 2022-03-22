<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Pigeon;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MakeOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function customer_can_create_order()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create();
        $order = OrderService::makeOrder($customer, 1, now()->addDay(2));

        $this->assertEquals($order->customer_id, $customer->id);
    }

    /** @test */
    function cant_create_order_if_there_is_no_pigeons_that_can_catchup_the_orders_deadline()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create([
            "speed_per_hour" => 5,
        ]);

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addHour(2));
    }

    /** @test */
    function cant_create_order_if_there_is_no_pigeons_with_maximum_range_greater_than_orders_distance()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create([
            "maximum_range" => 200,
        ]);

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addDays(5));
    }

    /** @test */
    function cant_create_order_if_all_pigeons_still_delivering()
    {
        $customer = Customer::factory()->create();
        Order::factory()->count(5)->onGoing()->create();

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addDays(5));
    }

    /** @test */
    function cant_create_order_if_all_available_pigeons_is_still_resting()
    {
        $customer = Customer::factory()->create();
        $pigeon = Pigeon::factory()->resting()->create([
            "previous_finished_order_time" => now()->subHour(),
            "downtime" => 2
        ]);
        Order::factory()->finished()->create([
            "assigned_pigeon_id" => $pigeon->id
        ]);

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addDays(5));
    }

    /** @test */
    function can_create_order_if_pigeons_is_available_and_dosnt_need_to_take_a_rest()
    {
        $customer = Customer::factory()->create();
        $pigeon = Pigeon::factory()->create();
        Order::factory()->finished()->create([
            "assigned_pigeon_id" => $pigeon->id
        ]);

        $order = OrderService::makeOrder($customer, 300, now()->addDays(5));

        $this->assertEquals($order->assigned_pigeon_id, $pigeon->id);
    }

    /** @test */
    function can_create_order_if_pigeons_is_available_and_finished_resting()
    {
        $customer = Customer::factory()->create();
        $pigeon = Pigeon::factory()->resting()->create([
            "previous_finished_order_time" => now()->subHours(5),
            "downtime" => 2
        ]);
        Order::factory()->finished()->create([
            "assigned_pigeon_id" => $pigeon->id
        ]);

        $order = OrderService::makeOrder($customer, 300, now()->addDays(5));

        $this->assertEquals($order->assigned_pigeon_id, $pigeon->id);
    }
}
