<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FinishOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function cant_finish_other_customers_order()
    {
        $customer = Customer::factory()->create();
        Sanctum::actingAs($customer);

        $order = Order::factory()->onGoing()->create();

        $response = $this->postJson(route('orders.finish', ["orderId" => $order->id]));

        $response->assertStatus(404);
    }

    /** @test */
    public function cant_finish_finished_order()
    {
        $customer = Customer::factory()->create();
        Sanctum::actingAs($customer);

        $order = Order::factory()->finished()->create([
            "customer_id" => $customer->id
        ]);

        $response = $this->postJson(route('orders.finish', ["orderId" => $order->id]));

        $response->assertStatus(404);
    }

    /** @test */
    public function cant_finish_unexist_order()
    {
        $customer = Customer::factory()->create();
        Sanctum::actingAs($customer);

        Order::factory()->onGoing()->count(10)->create([
            "customer_id" => $customer->id
        ]);

        $response = $this->postJson(route('orders.finish', ["orderId" => 999]));

        $response->assertStatus(404);
    }

    /** @test */
    public function can_finish_its_own_on_going_order()
    {
        $customer = Customer::factory()->create();
        Sanctum::actingAs($customer);

        $order = Order::factory()->onGoing()->create([
            "customer_id" => $customer->id
        ]);

        $response = $this->postJson(route('orders.finish', ["orderId" => $order->id]));

        $response->assertStatus(200);
    }

}
