<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $salesOrder->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            body { background-color: white !important; padding: 0 !important; }
            .print-hidden { display: none !important; }
            .receipt-shadow { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10">
    <!-- Print Button -->
    <button onclick="window.print()" class="fixed top-6 right-6 px-5 py-2.5 bg-slate-800 text-white rounded-xl shadow-lg hover:bg-slate-700 transition-all flex items-center gap-2 font-semibold text-sm print-hidden z-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Print Receipt
    </button>

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200 receipt-shadow">
        <!-- Receipt Header -->
        <div class="bg-slate-900 px-8 py-10 text-white flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <div class="flex items-center gap-3 mb-2 justify-center md:justify-start">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center p-1.5">
                        <img src="{{ asset('images/logo-white.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <h1 class="text-2xl font-black tracking-tightest">RM WOOD</h1>
                </div>
                <p class="text-slate-400 text-xs uppercase tracking-widest font-bold">Premium Wood Inventory & Management</p>
            </div>
            <div class="text-center md:text-right">
                <h2 class="text-4xl font-extrabold text-amber-500 mb-1">SALES RECEIPT</h2>
                <p class="text-slate-400 text-sm font-medium">Order Reference: <span class="text-white">{{ $salesOrder->order_number }}</span></p>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                <!-- Order Info -->
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Order Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Order Date</span>
                            <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($salesOrder->order_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Delivery Date</span>
                            <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($salesOrder->delivery_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Order Status</span>
                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase border border-blue-100">
                                {{ $salesOrder->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Customer Details</h3>
                    <div class="space-y-1">
                        <p class="text-base font-black text-slate-900">{{ $salesOrder->customer?->name ?? 'N/A' }}</p>
                        @if($salesOrder->customer?->email)
                            <p class="text-sm text-slate-500 font-medium">{{ $salesOrder->customer->email }}</p>
                        @endif
                        @if($salesOrder->customer?->phone)
                            <p class="text-sm text-slate-500 font-medium">{{ $salesOrder->customer->phone }}</p>
                        @endif
                        @if($salesOrder->customer?->address)
                            <p class="text-sm text-slate-500 font-medium mt-2 max-w-xs leading-relaxed italic border-l-2 border-amber-500 pl-3">
                                {{ $salesOrder->customer->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="rounded-xl border border-slate-200 overflow-hidden mb-10">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest">Description</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-center">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Unit Price</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($salesOrder->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <p class="text-sm font-bold text-slate-900">{{ $item->product?->product_name ?? 'Unknown Product' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">Product Category</p>
                            </td>
                            <td class="px-6 py-5 text-center text-sm font-bold text-slate-600">{{ number_format($item->quantity) }}</td>
                            <td class="px-6 py-5 text-right text-sm font-medium text-slate-600">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-5 text-right text-sm font-black text-slate-900">₱{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm italic font-medium">No order items listed.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Summary Section -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-12">
                <!-- Notes -->
                <div class="flex-1 max-w-md">
                    @if($salesOrder->note)
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Invoice Notes</h3>
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 italic text-sm text-slate-600 leading-relaxed font-medium">
                        "{{ $salesOrder->note }}"
                    </div>
                    @endif
                </div>

                <!-- Totals -->
                <div class="w-full md:w-80 space-y-4">
                    <div class="space-y-3 pb-4 border-b border-slate-100">
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="text-slate-900">₱{{ number_format($salesOrder->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-slate-500">Total Discount</span>
                            <span class="text-slate-900">₱0.00</span>
                        </div>
                    </div>
                    
                    <div class="bg-slate-900 p-4 rounded-xl text-white shadow-lg shadow-slate-900/10">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Grand Total</span>
                            <span class="text-xl font-black text-amber-500 tracking-tight">₱{{ number_format($salesOrder->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex justify-between items-center text-sm font-medium pt-2">
                            <span class="text-slate-500">Amount Paid</span>
                            <span class="text-green-600 font-bold">₱{{ number_format($salesOrder->paid_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 text-sm font-medium">Balance Payable</span>
                            <span class="text-base font-black {{ $salesOrder->total_amount - $salesOrder->paid_amount > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                ₱{{ number_format($salesOrder->total_amount - $salesOrder->paid_amount, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-end pt-1">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter shadow-sm border
                                {{ $salesOrder->payment_status === 'Paid' ? 'bg-green-100 text-green-700 border-green-200' : 
                                   ($salesOrder->payment_status === 'Pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-blue-100 text-blue-700 border-blue-200') }}">
                                {{ $salesOrder->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="mt-20 flex flex-col md:flex-row justify-between items-end gap-12">
                <div class="text-left space-y-4">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Authorized By</p>
                    <div class="border-b-2 border-slate-900 w-64 pb-2 italic text-slate-900 font-medium">
                        {{ auth()->user()->name }}
                    </div>
                    <p class="text-[9px] text-slate-400 italic">This is a system generated receipt and official signature is required for validity.</p>
                </div>
                <div class="text-right flex flex-col items-center md:items-end">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-slate-100 rounded flex items-center justify-center p-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain grayscale opacity-50">
                        </div>
                        <span class="text-xs font-black text-slate-300">RM WOOD SYSTEM</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Thank you for Choosing Excellence</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-4xl mx-auto mt-8 px-4 flex justify-between items-center text-slate-400">
        <p class="text-[10px] font-medium uppercase tracking-widest">Generated On: {{ now()->format('F d, Y @ h:i A') }}</p>
        <p class="text-[10px] font-medium uppercase tracking-widest">&copy; {{ date('Y') }} RM Wood Inventory Management</p>
    </div>
</body>
</html>
