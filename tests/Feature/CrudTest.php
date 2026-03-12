<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Customer;

class CrudTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_layanan_crud()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        // Test Read
        $response = $this->actingAs($admin)->get(route('layanan.index'));
        $response->assertStatus(200);

        // Test Create
        $payload = [
            'service_name' => 'Layanan Testing',
            'description' => 'Untuk test aja',
            'price' => 12000,
            'unit' => 'kg'
        ];
        $response = $this->actingAs($admin)->post(route('layanan.store'), $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('services', ['service_name' => 'Layanan Testing']);

        // Test Update
        $service = Service::where('service_name', 'Layanan Testing')->first();
        $updatePayload = [
            'service_name' => 'Layanan Testing Update',
            'description' => 'Untuk test aja 2',
            'price' => 15000,
            'unit' => 'pcs'
        ];
        $response = $this->actingAs($admin)->put(route('layanan.update', $service->id), $updatePayload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('services', ['service_name' => 'Layanan Testing Update', 'price' => 15000]);

        // Test Delete
        $response = $this->actingAs($admin)->delete(route('layanan.destroy', $service->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function test_pelanggan_crud()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('pelanggan.index'));
        $response->assertStatus(200);

        // Test Create
        $payload = [
            'full_name' => 'Budi Testing',
            'phone' => '08123456789',
            'address' => 'Jl. Test No 1',
            'status' => 'Reguler'
        ];
        $response = $this->actingAs($admin)->post(route('pelanggan.store'), $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['full_name' => 'Budi Testing', 'phone' => '08123456789']);

        $customer = Customer::where('full_name', 'Budi Testing')->first();

        // Test Validation (Error Mockup)
        // If we omit full_name which is required
        $invalidPayload = [
            'address' => 'No name'
        ];
        $response = $this->actingAs($admin)->put(route('pelanggan.update', $customer->id), $invalidPayload);
        $response->assertStatus(302); // Redirect back
        $response->assertSessionHasErrors(['full_name']); // Should have error for missing full_name

        // Valid Update
        $validPayload = [
            'full_name' => 'Update test',
            'phone' => '1234',
            'address' => 'Update addr',
            'status' => 'VIP'
        ];
        $response = $this->actingAs($admin)->put(route('pelanggan.update', $customer->id), $validPayload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Delete test
        $response = $this->actingAs($admin)->delete(route('pelanggan.destroy', $customer->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
