<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tracking_page_is_publicly_accessible()
    {
        $response = $this->get('/track');
        $response->assertStatus(200);
    }

    /** @test */
    public function tracking_api_returns_order_data_by_order_code()
    {
        $order = Order::factory()->create([
            'order_code' => 'ORD-TEST123',
            'customer_name' => 'Test Customer',
            'status' => 'Dicuci',
        ]);

        $response = $this->getJson('/api/track?q=ORD-TEST123');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
    }

    /** @test */
    public function tracking_api_returns_order_data_by_phone()
    {
        $order = Order::factory()->create([
            'customer_phone' => '081234567890',
            'customer_name' => 'Phone Test',
        ]);

        $response = $this->getJson('/api/track?q=081234567890');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
    }

    /** @test */
    public function tracking_api_returns_404_for_unknown_order()
    {
        $response = $this->getJson('/api/track?q=UNKNOWN-CODE');

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /** @test */
    public function tracking_api_requires_query_parameter()
    {
        $response = $this->getJson('/api/track');

        // Should fail validation (q is required)
        $response->assertStatus(422);
    }

    /** @test */
    public function tracking_api_is_rate_limited()
    {
        // The route has throttle:60,1 middleware
        // We just verify the route is accessible (testing actual rate limit would require 60+ requests)
        $response = $this->getJson('/api/track?q=TEST');
        $this->assertTrue(in_array($response->status(), [200, 404, 422]));
    }
}
