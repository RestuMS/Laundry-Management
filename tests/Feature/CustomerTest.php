<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /** @test */
    public function admin_can_view_customer_list()
    {
        Customer::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('pelanggan.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_customer()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('pelanggan.store'), [
                'full_name' => 'Ahmad Fauzi',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 10',
                'status' => 'Reguler',
            ]);

        $response->assertRedirect(route('pelanggan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['full_name' => 'Ahmad Fauzi']);
    }

    /** @test */
    public function admin_can_update_customer()
    {
        $customer = Customer::factory()->create(['full_name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
            ->put(route('pelanggan.update', $customer), [
                'full_name' => 'New Name',
                'phone' => '089876543210',
                'status' => 'VIP',
            ]);

        $response->assertRedirect(route('pelanggan.index'));
        $customer->refresh();
        $this->assertEquals('New Name', $customer->full_name);
        $this->assertEquals('VIP', $customer->status);
    }

    /** @test */
    public function admin_can_soft_delete_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('pelanggan.destroy', $customer));

        $response->assertRedirect(route('pelanggan.index'));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    /** @test */
    public function customer_store_validation_requires_full_name()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('pelanggan.store'), [
                'phone' => '081234567890',
            ]);

        $response->assertSessionHasErrors('full_name');
    }

    /** @test */
    public function customer_search_works()
    {
        Customer::factory()->create(['full_name' => 'Budi Santoso']);
        Customer::factory()->create(['full_name' => 'Siti Rahayu']);

        $response = $this->actingAs($this->admin)
            ->get(route('pelanggan.index', ['search' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    /** @test */
    public function customer_has_orders_relationship()
    {
        $customer = Customer::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $customer->orders);
    }
}
