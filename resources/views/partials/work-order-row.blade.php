@php
    $statusColors = [
        'pending' => 'bg-yellow-500',
        'in_progress' => 'bg-blue-500',
        'completed' => 'bg-green-500',
        'overdue' => 'bg-red-500',
        'cancelled' => 'bg-gray-500'
    ];
    
    $statusColor = $statusColors[$workOrder->status] ?? 'bg-gray-500';
    $statusLabel = ucwords(str_replace('_', ' ', $workOrder->status));
    $borderClass = 'border-slate-600'; // Default border

    // Dynamic Overdue Check
    $today = \Illuminate\Support\Carbon::today();
    $isOverdue = $workOrder->due_date && $workOrder->due_date->lt($today) && !in_array($workOrder->status, ['completed', 'cancelled', 'overdue']);

    if ($isOverdue) {
        $statusColor = 'bg-red-500';
        $statusLabel = 'Overdue';
        $borderClass = 'border-red-500'; // Highlight border
    }
@endphp
<div onclick="viewWorkOrder({{ $workOrder->id }})" class="p-4 border-2 {{ $borderClass }} rounded-xl hover:border-amber-500 hover:bg-slate-600/50 transition-all shadow-lg hover:shadow-xl backdrop-blur-sm cursor-pointer work-order-row" 
    data-order-number="{{ $workOrder->order_number }}" 
    data-product-name="{{ $workOrder->product_name }}" 
    data-customer-name="{{ strtolower($workOrder->salesOrder?->customer?->name ?? '') }}"
    data-assigned-to="{{ $workOrder->assigned_to }}" 
    data-status="{{ $workOrder->status }}">
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 bg-slate-800 text-slate-300 text-[10px] font-bold rounded uppercase tracking-wider border border-slate-600">
                    {{ $workOrder->salesOrder?->customer?->name ?? 'No Customer' }}
                </span>
            </div>
            <h3 class="font-bold text-white text-lg">{{ $workOrder->order_number }} • {{ $workOrder->product_name }}</h3>
            <p class="text-base text-slate-300 font-medium mt-1">Due: {{ $workOrder->due_date->format('m/d/Y') }} • Assigned: {{ $workOrder->assigned_to }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-4 py-2 {{ $statusColor }} text-white text-xs font-bold rounded-xl shadow-lg">
                {{ $statusLabel }}
            </span>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-4 mt-4 text-base">
        <div>
            <span class="text-slate-400 font-medium text-xs">Quantity</span>
            <p class="text-white font-bold text-lg mt-1">{{ $workOrder->quantity }} pcs</p>
        </div>
        <div>
            <span class="text-slate-400 font-medium text-xs">Starting Date</span>
            <p class="text-white font-bold text-lg mt-1">{{ $workOrder->created_at ? \Carbon\Carbon::parse($workOrder->created_at)->format('m/d/Y') : 'N/A' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-medium text-xs">Due Date</span>
            <p class="text-white font-bold text-lg mt-1">{{ $workOrder->due_date->format('m/d/Y') }}</p>
        </div>
        <div class="flex items-center space-x-2 justify-end">
            <button onclick="event.stopPropagation(); openCancelConfirmModal({{ $workOrder->id }}, '{{ $workOrder->order_number }}')" class="p-2.5 hover:bg-red-500/20 group rounded-lg transition-all" title="Cancel Order">
                <svg class="w-3.5 h-3.5 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <button onclick="event.stopPropagation(); openCompleteConfirmModal({{ $workOrder->id }}, '{{ $workOrder->order_number }}')" class="p-2.5 hover:bg-slate-500 rounded-lg transition-all" title="Complete">
                <svg class="w-3.5 h-3.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>