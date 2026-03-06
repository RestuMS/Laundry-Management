<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test the homepage returns successful UI landing view.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Home returns landing page HTTP 200
        $response->assertStatus(200);
    }
}
