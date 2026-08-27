<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {

        $response = $this->post('/register', [
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
            'email' => 'ivan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect();

        $user = \App\Models\User::where('email', 'ivan@example.com')->first();
        $this->assertEquals('ivan@example.com', $user->email);
    }
}
