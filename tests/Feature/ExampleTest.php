<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the home page redirects (to admin login).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // The home page redirects to admin login
        $response->assertRedirect();
    }

    /**
     * Test that the API config endpoint works.
     */
    public function test_api_config_endpoint(): void
    {
        $response = $this->get('/api/v1/config');

        $response->assertStatus(200);
    }
}
