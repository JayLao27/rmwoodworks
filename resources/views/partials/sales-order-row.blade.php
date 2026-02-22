@php
    $isArchive = $isArchive ?? false;
    $rowTextColor = $isArchive ? 'text-gray-700' : 'text-slate-300';
@endphp
<tr class="border-b border-slate-600 hover:bg-slate-700/50 transition-colors duration-200 cursor-pointer data-row" 
    data-order-id="{{ $order->id }}" 
    data-status="{{ $order->status }}" 
    data-payment="{{ $order->payment_status }}"
    data-order-date="{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d') }}"
    data-completed-date="{{ $order->updated_at->format('Y-m-d') }}"
    @if($isArchive) style="background-color: rgba(255,255,255,0.7);" @endif>
    <td class="px-4 py-3 font-mono {{ $rowTextColor }}">{{ $order->order_number }}</td>
    <td class="px-4 py-3">
        <div class="font-medium {{ $rowTextColor }}">{{ $order->customer?->name }}</div>
        @php $ct = $order->customer?->customer_type; $ctBg = $customerTypeBg[$ct] ?? '#e5e7eb'; @endphp
        <span class="mt-1 inline-block text-[10px] font-bold text-white px-2 py-0.5 rounded shadow-sm" style="background: {{ $ctBg }};">{{ strtoupper($ct) }}</span>
    </td>
    <td class="px-4 py-3 {{ $rowTextColor }}">{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
    @php
        $delivery = \Illuminate\Support\Carbon::parse($order->delivery_date);
        $due = $order->due_date ? \Illuminate\Support\Carbon::parse($order->due_date) : $delivery;
        $today = \Illuminate\Support\Carbon::today();
        $isDueToday = $due->isToday();
        $isOverdue = $due->lt($today);
        $diff = $due->diffInDays($today);
    @endphp
    @if(!$isArchive)
    <td class="px-4 py-3 font-medium">
        @if ($isDueToday)
            <span class="text-xs text-red-400">Due Today</span>
        @elseif ($isOverdue)
            <span class="text-xs text-red-500 font-bold underline">Overdue {{ $diff == 1 ? '1 day' : $diff . ' days' }}</span>
        @else
            @php $ahead = $today->diffInDays($due); @endphp
            <span class="text-xs text-amber-400">Due in {{ $ahead == 1 ? '1 day' : $ahead . ' days' }}</span>
        @endif
    </td>
    @endif
    <td class="px-4 py-3 {{ $rowTextColor }} text-center">
        {{ $isArchive ? $order->updated_at->format('M d, Y') : $delivery->format('M d, Y') }}
    </td>
    <td class="px-4 py-3">
        @php
        $statusColors = [
            'Pending' => $isArchive ? 'text-slate-500' : 'text-slate-400',
            'In production' => 'text-amber-600',
            'Confirmed' => 'text-blue-600',
            'Ready' => 'text-purple-600',
            'Delivered' => 'text-green-600',
            'Cancelled' => 'text-red-600 font-bold',
        ];
        $stColor = $statusColors[$order->status] ?? ($isArchive ? 'text-gray-700' : 'text-slate-300');
        @endphp
        <span class="text-xs font-bold {{ $stColor }}">{{ $order->status }}</span>
    </td>


    <td class="px-4 py-3 font-bold {{ $rowTextColor }}">₱{{ number_format($order->total_amount, 2) }}</td>
    <td class="px-4 py-3">
        @if($order->payment_status === 'Paid')
        <span class="text-xs font-bold text-green-600">{{ $order->payment_status }}</span>
        @elseif($order->payment_status === 'Partial')
        <span class="text-xs font-bold text-amber-600">{{ $order->payment_status }}</span>
        @else
        <span class="text-xs font-bold text-slate-500">{{ $order->payment_status }}</span>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="flex space-x-2 items-center justify-start">
            <button onclick="openModal('viewOrderModal-{{ $order->id }}')" class="p-1.5 {{ $isArchive ? 'hover:bg-gray-200 text-gray-600' : 'hover:bg-slate-500 text-white' }} rounded-lg transition-all" title="View">
                <svg class="w-4 h-4" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none">
                    <path d="M53.79,33.1a.51.51,0,0,0,0-.4C52.83,30.89,45.29,17.17,32,16.84S11,30.61,9.92,32.65a.48.48,0,0,0,0,.48C11.1,35.06,19.35,48.05,29.68,49,41.07,50,50.31,42,53.79,33.1Z"/>
                    <circle cx="31.7" cy="32.76" r="6.91"/>
                </svg>
            </button>
            @if(!in_array($order->status, ['Cancelled', 'Delivered']))
            <button onclick="openModal('editOrderModal-{{ $order->id }}')" class="p-1.5 {{ $isArchive ? 'hover:bg-gray-200 text-gray-600' : 'hover:bg-slate-500 text-white' }} rounded-lg transition-all" title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
            </button>
            @if($order->status === 'Ready' && $order->payment_status === 'Paid')
            <button type="button" onclick="openDeliverOrderModal({{ $order->id }}, '{{ $order->order_number }}')" class="p-1.5 {{ $isArchive ? 'hover:bg-gray-200 text-green-600' : 'hover:bg-slate-500 text-green-400' }} rounded-lg transition-all" title="Deliver Order">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
            </button>
            @endif
            @if(!in_array($order->payment_status, ['Paid', 'Partial']) && $order->status === 'Pending')
            <button type="button" onclick="openCancelSalesOrderModal({{ $order->id }}, '{{ $order->order_number }}')" class="p-1.5 {{ $isArchive ? 'hover:bg-gray-200 text-red-600' : 'hover:bg-slate-500 text-red-400' }} rounded-lg transition-all" title="Cancel">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
            </button>
            @endif
            @endif
        </div>
    </td>
</tr>

