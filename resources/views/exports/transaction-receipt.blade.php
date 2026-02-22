<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ $transactionId }}</title>
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
                <h2 class="text-4xl font-extrabold text-amber-500 mb-1 tracking-tight">TRANSACTION RECORD</h2>
                <p class="text-slate-400 text-sm font-medium">Ref: <span class="text-white">{{ $transactionId }}</span></p>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                <!-- Transaction Details -->
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Main Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Processing Date</span>
                            <span class="text-slate-900 font-bold tracking-tight">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Category</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border 
                                {{ $transaction->transaction_type === 'Income' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $transaction->transaction_type }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium tracking-tight">Internal ID</span>
                            <span class="text-slate-600 font-medium text-[11px] font-mono">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Entity Information -->
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Account Entity</h3>
                    <div class="space-y-1">
                        @if($transaction->salesOrder)
                            <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mb-1">Customer</p>
                            <p class="text-lg font-black text-slate-900 leading-tight">{{ $transaction->salesOrder->customer->name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 font-medium">Type: {{ $transaction->salesOrder->customer->customer_type ?? 'N/A' }}</p>
                        @elseif($transaction->purchaseOrder)
                            <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mb-1">Supplier</p>
                            <p class="text-lg font-black text-slate-900 leading-tight">{{ $transaction->purchaseOrder->supplier->name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 font-medium">Contact: {{ $transaction->purchaseOrder->supplier->contact_person ?? 'N/A' }}</p>
                        @else
                            <p class="text-base font-black text-slate-900">General Ledger Account</p>
                            <p class="text-sm text-slate-500 font-medium italic mt-1 leading-relaxed">Miscellaneous Transaction</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Transaction Amount Highlight -->
            <div class="bg-slate-50 rounded-2xl border-2 border-slate-100 p-10 mb-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 transform translate-x-12 -translate-y-12 rounded-full opacity-10 
                    {{ $transaction->transaction_type === 'Income' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
                    <div class="text-center md:text-left">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Amount Transacted</p>
                        <p class="text-5xl font-black tracking-tightest {{ $transaction->transaction_type === 'Income' ? 'text-green-600' : 'text-red-500' }}">
                            ₱{{ number_format($transaction->amount, 2) }}
                        </p>
                    </div>
                    <div class="h-16 w-px bg-slate-200 hidden md:block"></div>
                    <div class="text-center md:text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Verified Format</p>
                        <p class="text-slate-900 font-bold text-base bg-white px-4 py-1.5 rounded-lg shadow-sm border border-slate-100 italic">
                            {{ ucfirst(number_format($transaction->amount, 2)) }} Philippine Pesos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Related Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                @if($transaction->salesOrder)
                <div class="bg-blue-50/50 rounded-xl border border-blue-100 p-5 group hover:bg-blue-50 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest">Related Sales Order</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-blue-700/60 font-medium">Order Number</span>
                            <span class="font-bold text-blue-900">#{{ $transaction->salesOrder->order_number }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-blue-700/60 font-medium">Order Total</span>
                            <span class="font-bold text-blue-900">₱{{ number_format($transaction->salesOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                @elseif($transaction->purchaseOrder)
                <div class="bg-amber-50/50 rounded-xl border border-amber-100 p-5 hover:bg-amber-50 transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-amber-900 uppercase tracking-widest">Related Purchase Order</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-amber-700/60 font-medium">Order Number</span>
                            <span class="font-bold text-amber-900">#{{ $transaction->purchaseOrder->order_number }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-amber-700/60 font-medium">Order Total</span>
                            <span class="font-bold text-amber-900">₱{{ number_format($transaction->purchaseOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($transaction->description)
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 col-span-1 min-h-[100px]">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Internal Note</h4>
                    <p class="text-xs text-slate-600 italic leading-relaxed font-medium">"{{ $transaction->description }}"</p>
                </div>
                @endif
            </div>

            <!-- Footer Section -->
            <div class="mt-24 flex flex-col md:flex-row justify-between items-end gap-12">
                <div class="text-left space-y-4">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">Witnessed & Authenticated By</p>
                    <div class="border-b-2 border-slate-900 w-64 pb-2 italic text-slate-900 font-semibold">
                        {{ auth()->user()->name }}
                    </div>
                    <p class="text-[9px] text-slate-400 italic font-medium">Recorded at {{ now()->format('h:i A') }} within the RM Wood Financial Matrix.</p>
                </div>
                <div class="text-right flex flex-col items-center md:items-end">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 bg-slate-100 rounded flex items-center justify-center p-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain grayscale opacity-60">
                        </div>
                        <span class="text-xs font-black text-slate-300 tracking-widest leading-none">RM WOOD CORE</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Financial Integrity & Precision</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-4xl mx-auto mt-8 px-4 flex justify-between items-center text-slate-400">
        <p class="text-[10px] font-medium uppercase tracking-widest">Certified Record: {{ now()->format('M d, Y') }}</p>
        <p class="text-[10px] font-medium uppercase tracking-widest">Property of RM WOOD INVENTORY</p>
    </div>
</body>
</html>
