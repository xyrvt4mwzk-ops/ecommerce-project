<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_guest_marketplace_register_links_open_the_registration_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('register').'"', false);
        $this->assertMatchesRegularExpression(
            '/<a href="'.preg_quote(route('register'), '/').'"[^>]*>Start Selling<\/a>/',
            $response->getContent(),
        );
        $this->assertMatchesRegularExpression(
            '/<a href="'.preg_quote(route('register'), '/').'"[^>]*>Join as Affiliate<\/a>/',
            $response->getContent(),
        );

        $navigation = app(\App\Services\DashboardNavigation::class)->for(null);
        $register = collect($navigation['groups'])
            ->flatMap(fn (array $group) => $group['items'])
            ->firstWhere('label', 'Register');

        $this->assertSame(route('register'), $register['href']);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('role.select', absolute: false));
    }
}
