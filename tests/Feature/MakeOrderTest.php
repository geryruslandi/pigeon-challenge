<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MakeOrderTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function need_to_login_to_make_an_order() {
        $response = $this->postJson(route('orders.store'));

        $response->assertStatus(401);
    }

    /** @test */
    public function need_to_pass_distance_to_make_an_order() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'));

        $response->assertStatus(422);
        $response->assertSeeText('The distance field is required');
    }

    /** @test */
    public function need_to_pass_numeric_distance_to_make_an_order() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "non numeric distance"
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('The distance must be a number');
    }

    /** @test */
    public function need_to_pass_distance_greater_than_zero_to_make_an_order() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "0"
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('The distance must be at least 1');
    }

    /** @test */
    public function need_to_pass_deadline() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "1"
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('The deadline field is required');
    }

    /** @test */
    public function need_to_pass_correct_deadline_date_time_format() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "1",
            "deadline" => "2019-03-20"
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('The deadline does not match the format d-m-Y');
    }

    /** @test */
    public function minimum_deadline_should_now() {

        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "1",
            "deadline" => now()->subDay()->format('d-m-Y H:i')
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('The deadline must be a date after or equal to now');
    }

    /** @test */
    public function can_make_order_with_correct_deadline_and_distance() {
        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('orders.store'), [
            "distance" => "1",
            "deadline" => now()->addDay()->format('d-m-Y H:i')
        ]);

        $response->assertSuccessful();
    }
}
