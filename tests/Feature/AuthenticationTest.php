<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function need_to_pass_username_when_loggin_in()
    {
        $response = $this->postJson(route('login'));

        $response->assertStatus(422);
        $response->assertSee('The username field is required');
    }

    /** @test */
    public function need_to_pass_password_when_loggin_in()
    {
        $response = $this->postJson(route('login'));

        $response->assertStatus(422);
        $response->assertSee('The password field is required');
    }

    /** @test */
    public function cant_login_if_username_exist_but_password_doesnt_match()
    {
        Customer::factory()->create([
            'username' => 'gery',
            'password' => generatePassword('123123')
        ]);

        $response = $this->postJson(route('login'), [
            "username" => "gery",
            "password" => "password"
        ]);

        $response->assertStatus(422);
        $response->assertSee('Provided Credentials are incorrect');
    }

    /** @test */
    public function cant_login_if_username_doesnt_exist()
    {
        Customer::factory()->create([
            'username' => 'gery',
            'password' => generatePassword('123123')
        ]);

        $response = $this->postJson(route('login'), [
            "username" => "gerytest",
            "password" => "123123"
        ]);

        $response->assertStatus(422);
        $response->assertSee('Provided Credentials are incorrect');
    }

    /** @test */
    public function can_login_if_username_exist_and_password_match()
    {
        Customer::factory()->create([
            'username' => 'gery',
            'password' => generatePassword('123123')
        ]);

        $response = $this->postJson(route('login'), [
            "username" => "gery",
            "password" => "123123"
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data" => [
                "token"
            ]
        ]);
    }

    /** @test */
    public function cant_logout_if_not_logged_in()
    {
        $response = $this->postJson(route('logout'));

        $response->assertStatus(401);
    }

    /** @test */
    public function can_logout_if_logged_in()
    {
        Sanctum::actingAs(Customer::factory()->create());

        $response = $this->postJson(route('logout'));

        $response->assertStatus(200);
    }

    public function can_revoke_token_if_logging_out()
    {
        Sanctum::actingAs(Customer::factory()->create());

        $this->postJson(route('logout'));

        $this->assertCount(0, PersonalAccessToken::all());
    }
}
