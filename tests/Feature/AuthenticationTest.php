<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function need_to_pass_username_when_loggin_in()
    {
        $response = $this->postJson('/api/login');

        $response->assertStatus(422);
        $response->assertSee('The username field is required');
    }

    /** @test */
    public function need_to_pass_password_when_loggin_in()
    {
        $response = $this->postJson('/api/login');

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

        $response = $this->postJson('/api/login', [
            "username" => "gery",
            "password" => "password"
        ]);

        $response->assertStatus(422);
        $response->assertSee('Provided Credentials are incorrect');
    }
}
