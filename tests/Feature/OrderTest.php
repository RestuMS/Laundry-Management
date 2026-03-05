<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->kasir = User::factory()->kasir()->create();
    }

    /** @test */
    public function admin_can_view_order_list()
    {
        $response = $this->actingAs($this->admin)->get(route('order.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function kasir_can_view_order_list()
    {
        $response = $this->actingAs($this->kasir)->get(route('order.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_create_order_page()
    {
        Service::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('order.create'));
        $response->assertStatus(200);
        $response->assertSee('Informasi Transaksi Baru');
    }

    /** @test */
    public function admin_can_create_order_with_items()
    {
        $service = Service::factory()->create([
            'service_name' => 'Cuci Reguler',
            'price' => 7000,
            'unit' => 'Kg',
        ]);

        $response = $this->actingAs($this->admin)->post(route('order.store'), [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'estimated_finish' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'total_price' => 35000,
            'discount' => 0,
            'tax' => 0,
            'payment_method' => 'Cash',
            'payment_status' => 'Belum Bayar',
            'items' => [
                [
                    'service_name' => 'Cuci Reguler',
                    'qty' => 5,
                    'unit' => 'Kg',
                    'price' => 7000,
                    'subtotal' => 35000,
                ],
            ],
        ]);

        $response->assertRedirect(route('order.index'));
        $response->assertSessionHas('success');

        // Order created
        $this->assertDatabaseHas('orders', ['customer_name' => 'Budi Santoso']);

        // Customer auto-created
        $this->assertDatabaseHas('customers', ['full_name' => 'Budi Santoso']);

        // Order items created
        $this->assertDatabaseHas('order_items', ['service_name' => 'Cuci Reguler', 'qty' => 5]);
    }

    /** @test */
    public function order_links_to_existing_customer()
    {
        $customer = Customer::factory()->create([
            'full_name' => 'Siti Rahayu',
            'phone' => '081234567890',
            'total_orders' => 5,
        ]);

        $service = Service::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('order.store'), [
            'customer_id' => $customer->id,
            'customer_name' => 'Siti Rahayu',
            'customer_phone' => '081234567890',
            'estimated_finish' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'total_price' => 50000,
            'payment_status' => 'Lunas',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'service_name' => $service->service_name,
                    'qty' => 3,
                    'unit' => $service->unit,
                    'price' => $service->price,
                    'subtotal' => $service->price * 3,
                ],
            ],
        ]);

        $response->assertRedirect(route('order.index'));

        // Order linked to existing customer
        $order = Order::where('customer_name', 'Siti Rahayu')->first();
        $this->assertNotNull($order);
        $this->assertEquals($customer->id, $order->customer_id);

        // Customer total_orders incremented from 5 to 6
        $customer->refresh();
        $this->assertEquals(6, $customer->total_orders);
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->diterima()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('order.update_status', $order), [
                'status' => 'Dicuci',
            ]);

        $response->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('Dicuci', $order->status);
    }

    /** @test */
    public function admin_can_update_payment_status()
    {
        $order = Order::factory()->create(['payment_status' => 'Belum Bayar']);

        $response = $this->actingAs($this->admin)
            ->patch(route('order.update_payment_status', $order), [
                'payment_status' => 'Lunas',
            ]);

        $response->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('Lunas', $order->payment_status);
    }

    /** @test */
    public function order_scan_pickup_works()
    {
        $order = Order::factory()->create([
            'status' => 'Selesai',
            'payment_status' => 'DP',
        ]);

        $response = $this->actingAs($this->kasir)
            ->post(route('order.scan'), [
                'order_code' => $order->order_code,
            ]);

        $response->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('Diambil', $order->status);
        $this->assertEquals('Lunas', $order->payment_status);
    }

    /** @test */
    public function scan_pickup_fails_for_already_picked_up_order()
    {
        $order = Order::factory()->diambil()->create();

        $response = $this->actingAs($this->kasir)
            ->post(route('order.scan'), [
                'order_code' => $order->order_code,
            ]);

        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function admin_can_soft_delete_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('order.destroy', $order));

        $response->assertRedirect(route('order.index'));
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }

    /** @test */
    public function order_store_validation_fails_without_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('order.store'), []);

        $response->assertSessionHasErrors(['customer_name', 'total_price', 'payment_status', 'items']);
    }

    /** @test */
    public function owner_cannot_access_order_pages()
    {
        $owner = User::factory()->owner()->create();

        // CheckRole middleware redirects unauthorized users to their dashboard
        $response = $this->actingAs($owner)->get(route('order.index'));
        $response->assertRedirect('/owner');
    }
}
