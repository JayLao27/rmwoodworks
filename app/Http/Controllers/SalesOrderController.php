<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Services\CacheService;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        // Use pagination instead of loading all
        // Show orders that are 'Ready' and 'Paid' at the top
        $salesOrders = SalesOrder::with(['customer', 'items.product'])
            ->whereNotIn('status', ['Cancelled', 'Delivered'])
            ->orderByRaw("CASE WHEN status = 'Ready' AND payment_status = 'Paid' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        $archiveOrders = SalesOrder::with(['customer', 'items.product'])
            ->whereIn('status', ['Cancelled', 'Delivered'])
            ->latest()
            ->get();

        
        // Use cache for reference data
        $customers = CacheService::getCustomers();
        $products = CacheService::getProducts();

        return view('Systems.sales', compact('salesOrders', 'archiveOrders', 'customers', 'products'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        $maxRetries = 5;
        $retryCount = 0;
        $salesOrder = null;

        while ($retryCount < $maxRetries) {
            try {
                $orderNumber = $this->generateOrderNumber();

                $salesOrder = SalesOrder::create([
                    'order_number' => $orderNumber,
                    'customer_id' => $validated['customer_id'],
                    'order_date' => now()->toDateString(),
                    'delivery_date' => $validated['delivery_date'],
                    'due_date' => $validated['delivery_date'],
                    'status' => 'Pending',
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'payment_status' => 'Pending',
                    'note' => $validated['note'] ?? null,
                    'user_id' => auth()->id(),
                ]);

                // If creation succeeds, break the loop
                break;
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a duplicate entry error (SQLSTATE 23000)
                if ($e->getCode() == '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw $e;
                    }
                    // Wait a tiny bit to allow the other transaction to finish if needed
                    usleep(100000); // 100ms
                    continue;
                }
                throw $e;
            }
        }

        $totalAmount = 0;
        if ($salesOrder && !empty($validated['items'])) {
            $productIds = array_column($validated['items'], 'product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) { continue; }
                $unitPrice = (float) $product->selling_price;
                $quantity = (int) $item['quantity'];
                $lineTotal = $unitPrice * $quantity;

                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);
                $totalAmount += $lineTotal;
            }
        }

        if ($salesOrder) {
            $salesOrder->update(['total_amount' => $totalAmount]);
            \App\Models\SystemActivity::log('Sales', 'Order Created', "New Sales Order {$salesOrder->order_number} created for {$salesOrder->customer->name}.", 'indigo');
        }

        if ($request->wantsJson()) {
            // Eager load relationships for the partial
            $salesOrder->load(['customer', 'items.product']);

            // Define the same color maps used in the blade views
            $customerTypeBg = [
                'Wholesale' => '#64B5F6',
                'Retail' => '#6366F1',
                'Contractor' => '#BA68C8',
            ];
            $statusBg = [
                'In production' => '#FFB74D',
                'Pending' => '#64B5F6',
                'Delivered' => '#81C784',
                'Ready' => '#BA68C8',
            ];
            $paymentBg = [
                'Pending' => '#ffffff',
                'Partial' => '#FFB74D',
                'Paid' => '#81C784',
            ];

            $viewData = [
                'order' => $salesOrder,
                'customerTypeBg' => $customerTypeBg,
                'statusBg' => $statusBg,
                'paymentBg' => $paymentBg,
            ];
            
            // Render the table row partial
            $html = view('partials.sales-order-row', $viewData)->render();

            // Render the view/edit modals for this new order
            $modalHtml = view('partials.sales-order-modals', $viewData)->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Sales order created.',
                'html' => $html,
                'modalHtml' => $modalHtml,
            ]);
        }

        return redirect()->back()->with('success', 'Sales order created.');
    }

    public function cancelItem(Request $request, SalesOrderItem $item)
    {
        $request->validate([
            'cancel_quantity' => 'required|integer|min:1|max:' . ($item->quantity - $item->cancelled_quantity),
            'reason' => 'nullable|string'
        ]);

        $item->increment('cancelled_quantity', $request->cancel_quantity);
        
        return response()->json([
            'success' => true,
            'message' => 'Item quantity cancelled successfully.'
        ]);
    }

    public function cancelPurchaseItem(Request $request, PurchaseOrderItem $item)
    {
        $request->validate([
            'cancel_quantity' => 'required|numeric|min:0.01|max:' . ($item->quantity - $item->cancelled_quantity),
            'reason' => 'nullable|string'
        ]);

        $item->increment('cancelled_quantity', $request->cancel_quantity);
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase item quantity cancelled successfully.'
        ]);
    }


    public function update(Request $request, SalesOrder $sales_order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:In production,Pending,Delivered,Ready,Cancelled',

            'payment_status' => 'nullable|in:Pending,Partial,Paid',
            'note' => 'nullable|string',
        ]);

        // Restrict manual status edit if already Ready or Delivered
        $newStatus = $validated['status'] ?? $sales_order->status;
        if (in_array($sales_order->status, ['Ready', 'Delivered'])) {
            $newStatus = $sales_order->status;
        }

        $sales_order->update([
            'customer_id' => $validated['customer_id'],
            'delivery_date' => $validated['delivery_date'],
            'due_date' => $validated['delivery_date'],
            'status' => $newStatus,
            'payment_status' => $validated['payment_status'] ?? $sales_order->payment_status,
            'note' => $validated['note'] ?? null,
            'user_id' => auth()->id(),
        ]);

        \App\Models\SystemActivity::log('Sales', 'Order Updated', "Sales Order {$sales_order->order_number} details updated status: {$newStatus}.", 'indigo');

        if ($request->wantsJson()) {
            $sales_order->load(['customer', 'items.product']);

            $customerTypeBg = [
                'Wholesale' => '#64B5F6',
                'Retail' => '#6366F1',
                'Contractor' => '#BA68C8',
            ];
            $statusBg = [
                'In production' => '#FFB74D',
                'Pending' => '#64B5F6',
                'Delivered' => '#81C784',
                'Ready' => '#BA68C8',
            ];
            $paymentBg = [
                'Pending' => '#ffffff',
                'Partial' => '#FFB74D',
                'Paid' => '#81C784',
            ];

            $viewData = [
                'order' => $sales_order,
                'customerTypeBg' => $customerTypeBg,
                'statusBg' => $statusBg,
                'paymentBg' => $paymentBg,
            ];

            $html = view('partials.sales-order-row', $viewData)->render();
            $modalHtml = view('partials.sales-order-modals', $viewData)->render();

            return response()->json([
                'success' => true,
                'message' => 'Sales order updated successfully.',
                'html' => $html,
                'modalHtml' => $modalHtml,
                'orderId' => $sales_order->id,
            ]);
        }

        return redirect()->back()->with('success', 'Sales order updated.');
    }

public function RemoveCustomer($id)
    {
        $Customer = Customer::findOrFail($id);
        $customerName = $Customer->name;
        $Customer->delete();

        \App\Models\SystemActivity::log('Sales', 'Customer Deleted', "Customer '{$customerName}' removed from the system.", 'red');

        return redirect()->route('sales')->with('success', 'Customer deleted successfully!');
    }

    public function destroy(SalesOrder $sales_order)
    {
        $sales_order->update(['status' => 'Cancelled']);
        \App\Models\SystemActivity::log('Sales', 'Order Cancelled', "Sales Order {$sales_order->order_number} was cancelled.", 'red');
        return redirect()->back()->with('success', 'Order has been moved to Archive.');
    }

    public function deliver(SalesOrder $sales_order)
    {
        if ($sales_order->status !== 'Ready' || $sales_order->payment_status !== 'Paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only fully paid orders in "Ready" status can be delivered.'
            ], 400);
        }

        $sales_order->update(['status' => 'Delivered']);

        \App\Models\SystemActivity::log('Sales', 'Order Delivered', "Sales Order {$sales_order->order_number} has been delivered.", 'emerald');

        return response()->json([
            'success' => true,
            'message' => 'Order #' . $sales_order->order_number . ' has been marked as Delivered.'
        ]);
    }


    private function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'SO-' . $year . '-';

        $last = SalesOrder::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        $nextSeq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seqPart = end($parts);
            $num = (int) ltrim($seqPart, '0');
            $nextSeq = $num + 1;
        }

        do {
            $candidate = sprintf('%s%03d', $prefix, $nextSeq);
            $exists = SalesOrder::where('order_number', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
            $nextSeq++;
        } while (true);
    }

    public function exportReceipt($orderNumber)
    {
        $salesOrder = SalesOrder::with(['customer', 'items.product'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('exports.sales-receipt', compact('salesOrder'));
    }

    public function exportSalesReport()
    {
        $salesOrders = SalesOrder::with(['customer', 'items.product'])
            ->latest()
            ->get();

        $filename = 'sales-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($salesOrders) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Order Number',
                'Customer',
                'Customer Type',
                'Order Date',
                'Delivery Date',
                'Status',
                'Total Amount',
                'Payment Status',
                'Paid Amount',
            ]);

            // CSV Data
            foreach ($salesOrders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer?->name ?? 'N/A',
                    $order->customer?->customer_type ?? 'N/A',
                    $order->order_date,
                    $order->delivery_date,
                    $order->status,
                    number_format($order->total_amount, 2),
                    $order->payment_status,
                    number_format($order->paid_amount, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
