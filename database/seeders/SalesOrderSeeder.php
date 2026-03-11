<?php

namespace Database\Seeders;

use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderItem;
use App\Models\Inventory\Product;
use App\Models\Sales\Customer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            return;
        }

        $statuses = ['Pending', 'In production', 'Ready', 'Delivered', 'Cancelled'];
        $paymentStatuses = ['Pending', 'Partial', 'Paid'];

        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            
            // Logic: if delivered, payment is likely Paid
            if ($status === 'Delivered') {
                $paymentStatus = 'Paid';
            }

            $orderDate = Carbon::now()->subDays(rand(1, 60));
            $deliveryDate = (clone $orderDate)->addDays(rand(5, 20));

            $order = SalesOrder::create([
                'order_number' => 'SO-' . now()->year . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'order_date' => $orderDate,
                'delivery_date' => $deliveryDate,
                'total_amount' => 0, // Will update after adding items
                'paid_amount' => 0,
                'payment_status' => $paymentStatus,
                'status' => $status,
                'note' => 'Random sales order',
            ]);

            // Add 1-3 items
            $totalAmount = 0;
            $itemCount = rand(1, 4);

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->selling_price;
                $subtotal = $quantity * $unitPrice;

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'total_price' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            // Update order totals
            $paidAmount = 0;
            if ($paymentStatus === 'Paid') {
                $paidAmount = $totalAmount;
            } elseif ($paymentStatus === 'Partial') {
                $paidAmount = $totalAmount / 2;
            }

            $order->update([
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
            ]);
        }
    }
}