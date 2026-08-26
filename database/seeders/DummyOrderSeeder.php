<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a customer
        $customerUser = User::role('customer')->first();
        if (!$customerUser) {
             $customerUser = User::factory()->create();
             $customerUser->assignRole('customer');
        }
        
        $customer = Customer::firstOrCreate(['user_id' => $customerUser->id]);

        // Ensure we have a shop
        $shop = Shop::first();
        if (!$shop) {
            $shopUser = User::factory()->create();
            $shopUser->assignRole('shop');
            $shop = Shop::factory()->create(['user_id' => $shopUser->id]);
        }

        // Ensure we have products
        if (Product::count() < 3) {
            Product::factory()->count(5)->create(['shop_id' => $shop->id]);
        }
        $products = Product::limit(3)->get();

        // Create Orders with different statuses to simulate history
        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::PROCESSING,
            OrderStatus::ON_THE_WAY,
            OrderStatus::DELIVERED,
        ];

        foreach ($statuses as $index => $status) {
            $order = Order::factory()->create([
                'customer_id' => $customer->id,
                'shop_id' => $shop->id,
                'order_code' => 'DUMMY-' . rand(10000, 99999),
                'order_status' => $status->value,
                'tracking_id' => ($index > 1) ? 'TRK-' . strtoupper(uniqid()) : null, // Tracking ID for shipped/delivered
                'delivery_date' => ($status === OrderStatus::DELIVERED) ? now() : now()->addDays(3),
            ]);

            // Attach products
            foreach ($products as $product) {
                $order->products()->attach($product->id, [
                    'quantity' => rand(1, 3),
                    'price' => $product->price ?? 100,
                    'unit' => 'pc',
                    'color' => 'Red',
                    'size' => 'M',
                ]);
            }

            // Manually add history to simulate defined timeline if the Observer only captures the FINAL state
            // The observer creates one history on creation. 
            // If we want multiple history points for a delivered order, we need to add them manually backdated.
            
            if ($status === OrderStatus::DELIVERED) {
                 // It surely has Pending, Processing, On The Way
                 $order->histories()->create(['status' => OrderStatus::PENDING->value, 'created_at' => now()->subDays(3)]);
                 $order->histories()->create(['status' => OrderStatus::PROCESSING->value, 'created_at' => now()->subDays(2)]);
                 $order->histories()->create(['status' => OrderStatus::ON_THE_WAY->value, 'created_at' => now()->subDays(1)]);
                 // The 'created' event might have already added 'Delivered' at now()
            }
        }
        
        $this->command->info('Dummy orders seeded successfully.');
    }
}
