<!-- View Order Modal -->
<div id="viewOrderModal-{{ $order->id }}" class="fixed inset-0 bg-black/70 hidden overflow-y-auto" style="z-index: 99999; cursor: default;">
    <div class="rounded-lg shadow-2xl max-w-3xl w-[95%] mx-auto my-8 p-5" style="background-color: #FFF1DA;" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between mb-5 border-b-2 pb-6" style="border-color: #374151;">
            <div>
                <h3 class="text-xl font-bold" style="color: #374151;">Order #{{ $order->order_number }}</h3>
                <p class="mt-1" style="color: #666;">{{ $order->customer?->name }}</p>
            </div>
            <button class="text-xl transition" style="color: #999;" onclick="closeModal('viewOrderModal-{{ $order->id }}')">✕</button>
        </div>

        <!-- Order Information Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Customer Type</p>
                <p class="text-base font-bold mt-2" style="color: {{ $customerTypeBg[$order->customer?->customer_type] ?? '#374151' }};">{{ $order->customer?->customer_type }}</p>
            </div>
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                <p class="text-[10px] font-bold text-gray-500 uppercase">Started</p>
                <p class="text-base font-bold mt-1" style="color: #374151;">{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M d, Y') }}</p>
            </div>
            @if(in_array($order->status, ['Delivered', 'Cancelled']))
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #10B981;">
                <p class="text-[10px] font-bold text-green-600 uppercase">Completed</p>
                <p class="text-base font-bold mt-1 text-green-700">{{ $order->updated_at->format('M d, Y') }}</p>
            </div>
            @else
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Due Date</p>
                <p class="text-base font-bold mt-2" style="color: #374151;">{{ $order->due_date ? \Illuminate\Support\Carbon::parse($order->due_date)->format('M d, Y') : '-' }}</p>
            </div>
            @endif
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Delivery Date</p>
                <p class="text-base font-bold mt-2" style="color: #374151;">{{ \Illuminate\Support\Carbon::parse($order->delivery_date)->format('M d, Y') }}</p>
            </div>
            <div class="p-3 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Status</p>
                @php
                    $modalStatusColors = [
                        'Pending' => 'text-slate-500',
                        'In production' => 'text-amber-600',
                        'Confirmed' => 'text-blue-600',
                        'Ready' => 'text-purple-600',
                        'Delivered' => 'text-green-600',
                        'Cancelled' => 'text-red-600 font-bold',
                    ];
                    $mStColor = $modalStatusColors[$order->status] ?? 'text-slate-600';
                @endphp
                <p class="text-base font-bold mt-2 {{ $mStColor }}">{{ $order->status }}</p>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-5">
            <div class="p-3 rounded-lg border" style="background-color: rgba(255,255,255,0.7); border-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Payment Status</p>
                <p class="text-base font-bold mt-2 
                                @if($order->payment_status === 'Paid') text-green-600
                                @elseif($order->payment_status === 'Partial') text-orange-600
                                @else
                                @endif" style="@if($order->payment_status !== 'Paid' && $order->payment_status !== 'Partial')color: #374151;@endif">
                    {{ $order->payment_status }}
                </p>
            </div>
            <div class="p-3 rounded-lg border md:col-span-2" style="background-color: rgba(255,255,255,0.7); border-color: #374151;">
                <p class="text-xs font-semibold" style="color: #374151;">Total Amount</p>
                <p class="text-xl font-bold mt-2" style="color: #374151;">₱{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        <!-- Products Ordered -->
        <div class="mb-5">
            <h4 class="text-xl font-bold mb-4 flex items-center" style="color: #374151;">
                <span class="w-1 h-6 rounded mr-3" style="background-color: #374151;"></span>
                Products & Materials Ordered
            </h4>
            @if($order->items && $order->items->count() > 0)
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="p-5 rounded-lg border-l-4 hover:shadow-md transition" style="background-color: rgba(255,255,255,0.85); border-left-color: #374151;">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h5 class="font-bold text-base" style="color: #374151;">{{ $item->product?->product_name ?? 'Unknown Product' }}</h5>
                            <p class="text-xs mt-1" style="color: #666;">Unit Price: <span class="font-semibold" style="color: #374151;">₱{{ number_format($item->unit_price, 2) }}</span></p>
                        </div>
                        <div class="text-right">
                            <div class="inline-block px-3 py-1.5 rounded-full font-bold text-base" style="background-color: #374151; color: #FFF1DA;">
                                {{ $item->quantity }} <span class="text-xs">pcs</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 flex justify-between items-center" style="border-top: 1px solid #E8D5BF; color: #666;">
                        <span class="text-xs">Line Total:</span>
                        <span class="font-bold text-base" style="color: #374151;">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Order Summary -->
            <div class="mt-6 p-5 rounded-lg border-2 flex justify-between items-center" style="background-color: rgba(55,65,81,0.1); border-color: #374151;">
                <span class="text-base font-bold" style="color: #374151;">Order Total:</span>
                <span class="text-xl font-bold" style="color: #374151;">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
            @else
            <div class="p-3 rounded-lg border text-center" style="background-color: rgba(255,255,255,0.7); border-color: #374151; color: #999;">
                No products in this order
            </div>
            @endif
        </div>

        <!-- Notes Section -->
        @if($order->note)
        <div class="p-3 rounded-lg border-l-4 mb-5" style="background-color: rgba(255,255,255,0.85); border-left-color: #374151;">
            <p class="font-semibold mb-2" style="color: #374151;">Notes</p>
            <p class="whitespace-pre-wrap" style="color: #666;">{{ $order->note }}</p>
        </div>
        @endif

        <!-- Close Button -->
        <div class="flex justify-end">
            <button class="px-6 py-3 rounded-lg font-semibold transition text-white" style="background-color: #374151;" onclick="closeModal('viewOrderModal-{{ $order->id }}')">Close</button>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div id="editOrderModal-{{ $order->id }}" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden" style="z-index: 99999; cursor: default;" onclick="if(event.target === this) closeModal('editOrderModal-{{ $order->id }}')">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-amber-50 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700" onclick="event.stopPropagation()">
            <div class="p-4">
                <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                    <h3 class="text-xl font-bold text-gray-900">Edit Order {{ $order->order_number }}</h3>
                    <button onclick="closeModal('editOrderModal-{{ $order->id }}')" class="text-gray-700 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('sales-orders.update', $order) }}" onsubmit="return submitEditOrder(event, {{ $order->id }})">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-3">Customer</label>
                            <input type="text" value="{{ $order->customer->name }} ({{ $order->customer->customer_type }})" class="text-black w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 bg-gray-100 text-sm transition-all" readonly>
                            <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Delivery Date</label>
                                <input type="date" name="delivery_date" min="{{ date('Y-m-d') }}" value="{{ $order->delivery_date }}" class="editDeliveryDate w-full border-2 text-black border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all">
                            </div>
                        </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Notes</label>
                                <textarea name="note" class="w-full border-2 text-black border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" rows="2">{{ $order->note }}</textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all" onclick="closeModal('editOrderModal-{{ $order->id }}')">Cancel</button>
                            <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">Update Order</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
