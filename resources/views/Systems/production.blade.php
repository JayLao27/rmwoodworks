@extends('layouts.system')

@section('main-content')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
	<!-- Header Section -->
	<div class="p-5 bg-amber-50 border-b border-amber-200 relative z-10">
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-xl font-bold text-gray-800">Production Management</h1>
				<p class="text-xs font-medium text-gray-600 mt-2">Plan, track, and manage furniture production workflow</p>
			</div>
			<button onclick="openProductionHistoryModal()" class="px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-600 text-white rounded-lg hover:from-slate-600 hover:to-slate-500 transition-all shadow-lg hover:shadow-xl flex items-center gap-2 font-medium text-sm">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
				</svg>
				<span>Production History</span>
			</button>
		</div>
	</div>
    <div class="flex-1 p-5 bg-amber-50 overflow-y-auto">

			<!-- Status Overview Cards -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6 px-5">
				<!-- Pending Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Pending</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-yellow-300 to-yellow-100 bg-clip-text text-transparent">{{ $statusCounts['pending'] ?? 0 }}</p>
							<p class="text-slate-400 text-xs mt-1">Awaiting start</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
								@include('components.icons.time', ['class' => 'w-5 h-5 text-yellow-400'])  
						</div>
					</div>
				</div>

				<!-- In Progress Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">In Progress</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-blue-300 to-blue-100 bg-clip-text text-transparent">{{ $statusCounts['in_progress'] ?? 0 }}</p>
							<p class="text-slate-400 text-xs mt-1">Currently working</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.tool', ['class' => 'w-5 h-5 text-blue-400'])
						</div>
					</div>
				</div>

				<!-- Completed Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Completed</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-green-300 to-green-100 bg-clip-text text-transparent">{{ $statusCounts['completed'] ?? 0 }}</p>
							<p class="text-slate-400 text-xs mt-1">This month</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.checkmark', ['class' => 'w-5 h-5 text-green-400'])
						</div>
					</div>
				</div>

				<!-- Overdue Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Overdue</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-red-300 to-red-100 bg-clip-text text-transparent">{{ $statusCounts['overdue'] ?? 0 }}</p>
							<p class="text-slate-400 text-xs mt-1">Requires attention</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.alert', ['class' => 'w-5 h-5 text-red-400'])
						</div>
					</div>
				</div>
			</div>

            
			<!-- Main Content Container (with Padding) -->
			<div class="px-5 pb-5">
				<!-- Production Workflow Section -->
				<section class="bg-gradient-to-br from-slate-700 to-slate-800 text-white p-3 rounded-xl overflow-visible shadow-xl border border-slate-600">
					<header class="flex justify-between items-center mb-6">
						<div>
							<h2 class="text-xl font-bold text-white">Production Workflow</h2>
							<p class="text-slate-300 text-xs font-medium mt-2">Manage your raw materials and finished products</p>
						</div>
						<div class="flex space-x-3">
							<button onclick="openWorkOrderModal()" class="relative px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-2 font-medium">
								<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
								</svg>
								<span>Work Orders</span>
								@if($pendingItemsCount > 0)
								<span class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">{{ $pendingItemsCount }}</span>
								@endif
							</button>
						</div>
					</header>

					<!-- Search Bar and Status Filter -->
					<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
						<div class="flex-1 max-w-md">
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
									<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
									</svg>
								</div>
								<input id="workOrderSearchInput" type="text" placeholder="Search by order #, product, customer..." oninput="filterWorkOrders()" class="w-full pl-10 pr-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400">
							</div>
						</div>
						<div class="w-full md:w-auto">
							<select id="statusFilterSelect" onchange="filterWorkOrders()" class="w-full md:w-auto bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
								<option value="all">All Status</option>
								<option value="in_progress">In Progress</option>
								<option value="overdue">Overdue</option>
							</select>
						</div>
					</div>

					<!-- Work Orders List -->
					@php
						$visibleWorkOrders = ($workOrders ?? collect())->whereIn('status', ['in_progress', 'overdue']);
					@endphp
					<!-- Work Orders List -->
					<div class="space-y-3 overflow-y-auto custom-scrollbar" style="max-height:60vh;" id="workOrderTableBody">
						@foreach($visibleWorkOrders as $workOrder)
							@include('partials.work-order-row', ['workOrder' => $workOrder])
						@endforeach
						<div id="workOrderEmptyState" class="py-12 px-4 text-center text-slate-400 {{ $visibleWorkOrders->count() > 0 ? 'hidden' : '' }}">
							No active production found
						</div>
					</div>
				</section>
			</div>
        </div>

        <!-- Add Work Order Modal -->
        <div id="workOrderModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeWorkOrderModal()">
            <div class="modal-content bg-amber-50 rounded-2xl max-w-2xl w-full shadow-2xl transform transition-all animate-fadeIn" onclick="event.stopPropagation()">
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-300 px-6 py-5 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-gray-800 mb-1">Create Work Order</h3>
                                @if($pendingItemsCount > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ $pendingItemsCount }}
                                </span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm mt-0.5">Select a sales order and assign to a production team</p>
                        </div>
                    </div>
                    <button onclick="closeWorkOrderModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg p-2 transition-all duration-200 group">
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Sales Orders List -->
                    <div id="salesOrdersListSection" class="space-y-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Pending Sales Orders</h4>
                            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $pendingItemsCount }} Products Pending</span>
                        </div>

                        <!-- Search Input -->
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="modalSalesOrderSearch" placeholder="Search by order # or customer..." 
                                   class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-700 placeholder-gray-400 shadow-sm transition-all"
                                   oninput="filterModalSalesOrders()">
                        </div>
                        
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($pendingSalesOrders ?? [] as $salesOrder)
                                @php
                                    $itemsData = $salesOrder->items->filter(function($item) use ($salesOrder) {
                                        return !$salesOrder->workOrders->where('product_id', $item->product_id)
                                                                      ->where('status', '!=', 'cancelled')
                                                                      ->isNotEmpty();
                                    })->map(fn($item) => [
                                        'id' => $item->product_id, 
                                        'name' => addslashes($item->product->product_name ?? 'Product'), 
                                        'quantity' => $item->quantity
                                    ])->values();
                                @endphp
                                <div onclick="selectSalesOrder({{ $salesOrder->id }}, '{{ addslashes($salesOrder->order_number) }}', '{{ addslashes($salesOrder->customer->name ?? '') }}', '{{ $salesOrder->delivery_date }}', {{ json_encode($itemsData) }})" 
                                     class="sales-order-card group relative p-5 border-2 border-gray-300 rounded-xl hover:border-blue-400 hover:shadow-lg cursor-pointer transition-all duration-300 bg-white hover:bg-blue-50"
                                     data-order-number="{{ strtolower($salesOrder->order_number) }}"
                                     data-customer-name="{{ strtolower($salesOrder->customer->name ?? '') }}">
                                    
                                    <!-- Actions -->
                                    <div class="absolute top-5 right-5 flex items-center gap-2">
                                        <button type="button" onclick="event.stopPropagation(); openModal('viewOrderModal-{{ $salesOrder->id }}')" class="p-1.5 bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 rounded-lg transition-all" title="View Details">
                                            <svg class="w-4 h-4" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none">
                                                <path d="M53.79,33.1a.51.51,0,0,0,0-.4C52.83,30.89,45.29,17.17,32,16.84S11,30.61,9.92,32.65a.48.48,0,0,0,0,.48C11.1,35.06,19.35,48.05,29.68,49,41.07,50,50.31,42,53.79,33.1Z"/>
                                                <circle cx="31.7" cy="32.76" r="6.91"/>
                                            </svg>
                                        </button>
                                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                            </svg>
                                            {{ $salesOrder->items->count() }} {{ $salesOrder->items->count() === 1 ? 'item' : 'items' }}
                                        </span>
                                    </div>
                                    
                                    <div class="pr-28">
                                        <!-- Order Info -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="inline-flex items-center gap-1.5 text-gray-700 font-bold text-base">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                                </svg>
                                                {{ $salesOrder->order_number }}
                                            </span>
                                        </div>
                                        
                                        <!-- Customer -->
                                        <div class="flex items-center gap-2 mb-3">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ $salesOrder->customer->name }}</span>
                                        </div>
                                        
                                        <!-- Items List -->
                                        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wide">Products:</p>
                                            <div class="space-y-1.5">
                                                @foreach($salesOrder->items as $item)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-700 font-medium">{{ $item->product->product_name ?? 'Product' }}</span>
                                                        <span class="bg-white px-2.5 py-1 rounded-md text-xs font-semibold text-gray-600 border border-gray-200">Qty: {{ $item->quantity }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <!-- Delivery Date -->
                                        <div class="flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-gray-600">Delivery: <strong class="text-gray-800">{{ \Carbon\Carbon::parse($salesOrder->delivery_date)->format('M d, Y') }}</strong></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Hover Arrow -->
                                    <div class="absolute right-5 bottom-5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No pending sales orders available</p>
                                    <p class="text-gray-400 text-sm mt-1">New orders will appear here</p>
                                </div>
                            @endforelse

                            <!-- No matching search results in modal -->
                            <div id="modalNoResults" class="text-center py-12 hidden">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-50 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">No matching orders found</p>
                                <p class="text-gray-400 text-sm mt-1">Try a different search term</p>
                            </div>
                        </div>
                    </div>

                    <!-- Team Assignment Form (Hidden by default) -->
                    <form id="workOrderForm" method="POST" action="{{ route('production.store') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="sales_order_id" id="formSalesOrderId">
                        <input type="hidden" name="product_id" id="formProductId">
                        <input type="hidden" name="quantity" id="formQuantity">
                        <input type="hidden" name="due_date" id="formDueDate">
                        <input type="hidden" name="priority" value="medium">
                        
                        <div class="space-y-6">
                            <!-- Order Info -->
                            <div class="bg-blue-50 rounded-xl border-2 border-blue-200 p-5 shadow-sm">
                                <div class="flex flex-wrap gap-4 justify-between items-center mb-0">
                                    <div>
                                        <span class="text-xs text-gray-500 font-semibold uppercase block mb-1">Order #</span>
                                        <span id="summaryOrderNumber" class="font-bold text-gray-800 text-lg block">-</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 font-semibold uppercase block mb-1">Customer</span>
                                        <span id="summaryCustomer" class="font-bold text-gray-800 text-base block">-</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 font-semibold uppercase block mb-1">Delivery</span>
                                        <span id="summaryDeliveryDate" class="font-bold text-gray-800 text-base block">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Products to Produce -->
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-3 uppercase tracking-wide">
                                    Select Products to Manufacture
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                                    <input type="checkbox" id="selectAllItems" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked onclick="toggleAllItems(this)">
                                                </th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderItemsTableBody" class="bg-white divide-y divide-gray-200">
                                            <!-- Valid items will be injected here via JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <p id="itemSelectionError" class="text-red-500 text-sm mt-2 hidden">Please select at least one product.</p>
                            </div>

                            <!-- Team Assignment -->
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2 uppercase tracking-wide">
                                    Assign To Team 
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" onclick="selectTeam(this, 'Team A')"
                                        class="team-btn flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 shadow-sm">
                                        🔨 Team A
                                    </button>
                                    <button type="button" onclick="selectTeam(this, 'Team B')"
                                        class="team-btn flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 shadow-sm">
                                        🔨 Team B
                                    </button>
                                    <button type="button" onclick="selectTeam(this, 'Team C')"
                                        class="team-btn flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-gray-300 rounded-xl text-sm font-bold text-gray-700 hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 shadow-sm">
                                        🔨 Team C
                                    </button>
                                </div>
                                <input type="hidden" name="assigned_to" id="selectedTeam" required>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center gap-3 mt-8 pt-6 border-t-2 border-gray-300">
                            <button type="button" onclick="resetWorkOrderForm()" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-base font-semibold text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </button>
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-all duration-200 text-base font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Create Work Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Custom Scrollbar Styles -->
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            .animate-fadeIn {
                animation: fadeIn 0.2s ease-out;
            }
        </style>

        <!-- Edit Work Order Modal -->
        <div id="editWorkOrderModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target === this) closeEditWorkOrderModal()">
            <div class="modal-content bg-amber-50 rounded-xl max-w-lg w-[92%] shadow-2xl transform transition-all" onclick="event.stopPropagation()">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-300">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Update Work Order</h3>
                            <p class="text-gray-600 text-base">Update the status and completion details.</p>
                        </div>
                        <button onclick="closeEditWorkOrderModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-2 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                        
                        <form id="editWorkOrderForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-gray-700 text-lg font-medium mb-3">Status</label>
                                    <select name="status" id="editStatus" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg" required>
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="quality_check">Quality Check</option>
                                        <option value="completed">Completed</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-lg font-medium mb-3">Completion Quantity</label>
                                    <input type="number" name="completion_quantity" id="editCompletionQuantity" min="0" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg" required>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-300">
                                <button type="button" onclick="closeEditWorkOrderModal()" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition-all duration-200 text-base font-medium">
                                    Cancel
                                </button>
                                <button type="submit" class="px-8 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-all duration-200 text-base font-medium shadow-md hover:shadow-lg">
                                    Update Work Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

            <!-- View Work Order Modal -->
            <div id="viewWorkOrderModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeViewWorkOrderModal()">
                <div class="modal-content bg-amber-50 rounded-xl max-w-[700px] w-full shadow-2xl transform transition-all animate-fadeIn" onclick="event.stopPropagation()">
                    <div class="p-3.5">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4 border-b-2 pb-3.5" style="border-color: #374151;">
                            <div>
                                <h3 class="text-base font-bold" style="color: #374151;">Work Order Details</h3>
                                <p class="text-xs mt-0.5" style="color: #666;">Summary and status</p>
                            </div>
                            <button onclick="closeViewWorkOrderModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-1.5 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Work Order Information Cards -->
                        <div class="grid grid-cols-2 gap-2.5 mb-4">
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                                <p class="text-xs font-semibold" style="color: #374151;">Order #</p>
                                <p id="vw_orderNumber" class="text-base font-bold mt-1.5" style="color: #374151;">-</p>
                            </div>
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                                <p class="text-xs font-semibold" style="color: #374151;">Product</p>
                                <p id="vw_productName" class="text-base font-bold mt-1.5" style="color: #374151;">-</p>
                            </div>
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #F57C00;">
                                <p class="text-xs font-semibold" style="color: #E65100;">Quantity</p>
                                <p id="vw_quantity" class="text-base font-bold mt-1.5" style="color: #E65100;">-</p>
                            </div>
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #1976D2;">
                                <p class="text-xs font-semibold" style="color: #0D47A1;">Start Date</p>
                                <p id="vw_startingDate" class="text-base font-bold mt-1.5" style="color: #0D47A1;">-</p>
                            </div>
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #C2185B;">
                                <p class="text-xs font-semibold" style="color: #880E4F;">Due Date</p>
                                <p id="vw_dueDate" class="text-base font-bold mt-1.5" style="color: #880E4F;">-</p>
                            </div>
                            <div class="p-2.5 rounded-lg border-l-4" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                                <p class="text-xs font-semibold" style="color: #374151;">Assigned To</p>
                                <p id="vw_assignedTo" class="text-base font-bold mt-1.5" style="color: #374151;">-</p>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-4 p-3 px-1 rounded-lg border-l-4" style="background-color: rgba(255, 255, 255, 0.7); border-left-color: #374151;">
                            <p class="text-xs font-semibold mb-2.5" style="color: #374151;">Notes</p>
                            <form id="notesForm" method="POST" action="" class="space-y-2.5">
                                @csrf
                                @method('PUT')
                                <textarea id="vw_notes" name="notes" rows="3" class="w-full bg-white border-2 border-gray-300 rounded-lg px-2.5 py-2 text-gray-700 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs" placeholder="Add notes..."></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-xs font-medium shadow-sm hover:shadow-md" title="Save notes">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Close Button -->
                        <div class="">
                            <button type="button" onclick="closeViewWorkOrderModal()" class="px-3.5 py-1.5 rounded-lg hover:shadow-lg transition-all duration-200 text-xs font-medium text-white" style="background-color: #374151;">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Complete Work Order Confirmation Modal -->
        <div id="completeConfirmModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeCompleteConfirmModal()">
            <div class="modal-content bg-amber-50 rounded-lg max-w-xs w-full shadow-2xl transform transition-all animate-fadeIn" onclick="event.stopPropagation()">
                <div class="p-4">
                    <!-- Icon -->
                    <div class="flex justify-center mb-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Message -->
                    <h3 class="text-center font-bold text-sm mb-1" style="color: #374151;">Complete Work Order?</h3>
                    <p class="text-center text-xs mb-4" style="color: #666;">
                        Mark work order <span id="completeConfirmOrderNumber" class="font-semibold">-</span> as completed?
                    </p>

                    <!-- Actions -->
                    <div class="flex justify-center gap-2">
                        <button type="button" onclick="closeCompleteConfirmModal()" class="px-4 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition-all text-xs font-medium text-gray-700">
                            No, Keep Working
                        </button>
                        <button type="button" onclick="confirmCompleteWorkOrder()" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-xs font-medium shadow-md hover:shadow-lg">
                            Yes, Complete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Work Order Confirmation Modal -->
        <div id="cancelConfirmModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeCancelConfirmModal()">
            <div class="modal-content bg-amber-50 rounded-lg max-w-xs w-full shadow-2xl transform transition-all animate-fadeIn" onclick="event.stopPropagation()">
                <div class="p-4">
                    <!-- Icon -->
                    <div class="flex justify-center mb-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Message -->
                    <h3 class="text-center font-bold text-sm mb-1" style="color: #374151;">Cancel Work Order?</h3>
                    <p class="text-center text-xs mb-4" style="color: #666;">
                        Are you sure you want to cancel work order <span id="cancelConfirmOrderNumber" class="font-semibold">-</span>? This will release all reserved materials.
                    </p>

                    <!-- Actions -->
                    <div class="flex justify-center gap-2">
                        <button type="button" onclick="closeCancelConfirmModal()" class="px-4 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition-all text-xs font-medium text-gray-700">
                            No, Keep It
                        </button>
                        <button type="button" onclick="confirmCancelWorkOrder()" class="px-4 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all text-xs font-medium shadow-md hover:shadow-lg">
                            Yes, Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production History Modal -->
        <div id="productionHistoryModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="if(event.target === this) closeProductionHistoryModal()">
            <div class="modal-content bg-amber-50 rounded-xl max-w-3xl w-full shadow-2xl transform transition-all animate-fadeIn" onclick="event.stopPropagation()">
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-3 pb-3 border-b border-gray-300 px-4 py-3 rounded-t-xl">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Production History</h3>
                            <p class="text-gray-600 text-xs">View completed work orders</p>
                        </div>
                    </div>
                    <button onclick="closeProductionHistoryModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg p-1.5 transition-all duration-200 group">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4">
                    <!-- Filter Section -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Completed Work Orders</h4>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full" id="completedCount">{{ $workOrders->where('status', 'completed')->count() }} Completed</span>
                        </div>
                        
                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                            <!-- Search -->
                            <div class="col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                                <input type="text" id="historySearchInput" placeholder="Order #, Product..." class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
                            </div>
                            <!-- Start Date -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">From Date</label>
                                <input type="date" id="historyStartDate" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
                            </div>
                            <!-- End Date -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">To Date</label>
                                <input type="date" id="historyEndDate" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
                            </div>
                        </div>
                        <!-- Quick Filters -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <button onclick="applyHistoryQuickFilter('yesterday')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Yesterday</button>
                            <button onclick="applyHistoryQuickFilter('last-week')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Last Week</button>
                            <button onclick="applyHistoryQuickFilter('1-month')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Month</button>
                            <button onclick="applyHistoryQuickFilter('1-year')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Year</button>
                        </div>

                        <!-- Clear Filters Button -->
                        <div class="flex justify-end mb-2">
                            <button onclick="clearHistoryFilters()" class="inline-flex items-center gap-1 px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-all text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- History List -->
                    <div class="space-y-2 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar" id="historyListContainer">
                        @forelse($workOrders->where('status', 'completed') as $workOrder)
                            <div onclick="viewWorkOrder({{ $workOrder->id }})" class="history-item group relative p-3 border border-gray-300 rounded-lg bg-white shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer" 
                                 data-order-number="{{ strtolower($workOrder->order_number) }}" 
                                 data-product-name="{{ strtolower($workOrder->product_name) }}" 
                                 data-assigned-to="{{ strtolower($workOrder->assigned_to) }}" 
                                 data-completed-date="{{ $workOrder->updated_at->format('Y-m-d') }}"
                                 data-starting-date="{{ $workOrder->starting_date ? \Carbon\Carbon::parse($workOrder->starting_date)->format('Y-m-d') : '' }}">
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-0.5 rounded-md text-xs font-semibold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Completed
                                    </span>
                                </div>

                                <div class="pr-24">
                                    <!-- Order Number -->
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="inline-flex items-center gap-1 text-gray-700 font-bold text-sm">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                            {{ $workOrder->order_number }}
                                        </span>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex items-center gap-1.5 mb-2">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-700">{{ $workOrder->product_name }}</span>
                                    </div>

                                    <!-- Details Grid -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 bg-gray-50 rounded-md p-2 mb-2 border border-gray-100">
                                        <div>
                                            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Quantity</p>
                                            <p class="text-xs font-bold text-gray-800">{{ $workOrder->quantity }} pcs</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Assigned</p>
                                            <p class="text-xs font-bold text-gray-800">{{ $workOrder->assigned_to }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-semibold text-blue-500 uppercase mb-0.5">Started</p>
                                            <p class="text-xs font-bold text-gray-800">{{ $workOrder->starting_date ? \Carbon\Carbon::parse($workOrder->starting_date)->format('M d') : 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-semibold text-green-600 uppercase mb-0.5">Completed</p>
                                            <p class="text-xs font-bold text-gray-800">{{ $workOrder->updated_at->format('M d') }}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div id="historyEmptyState" class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium text-sm">No completed work orders yet</p>
                                <p class="text-gray-400 text-xs mt-1">Completed orders will appear here</p>
                            </div>
                        @endforelse
                        <div id="historyNoResults" class="text-center py-8 hidden">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium text-sm">No work orders match your filters</p>
                            <p class="text-gray-400 text-xs mt-1">Try adjusting your search or date range</p>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-300">
                        <button type="button" onclick="closeProductionHistoryModal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-all duration-200 text-sm font-semibold shadow-md hover:shadow-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Scrollbar Styles -->
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #475569;
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #f59e0b;
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #d97706;
            }
        </style>

            <script>
            // Modal functions with animations
            function openWorkOrderModal() {
                const modal = document.getElementById('workOrderModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    const content = modal.querySelector('.modal-content');
                    if (content) {
                        content.style.opacity = '0';
                        content.style.transform = 'scale(0.95) translateY(10px)';
                        setTimeout(() => {
                            content.style.transition = 'all 0.2s ease-out';
                            content.style.opacity = '1';
                            content.style.transform = 'scale(1) translateY(0)';
                        }, 10);
                    }
                }, 10);
            }

            function closeWorkOrderModal() {
                const modal = document.getElementById('workOrderModal');
                const content = modal.querySelector('.modal-content');
                if (content) {
                    content.style.transition = 'all 0.15s ease-in';
                    content.style.opacity = '0';
                    content.style.transform = 'scale(0.95) translateY(10px)';
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.getElementById('workOrderForm').reset();
                        document.getElementById('salesOrdersListSection').classList.remove('hidden');
                        document.getElementById('productLineSelector').classList.add('hidden');
                    }, 150);
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.getElementById('workOrderForm').reset();
                    // Clear search input and results in modal
                    const searchInput = document.getElementById('modalSalesOrderSearch');
                    if (searchInput) {
                        searchInput.value = '';
                        filterModalSalesOrders();
                    }
                }
            }

            const rawWorkOrdersData = @json($workOrders ?? []);
            const workOrdersData = (rawWorkOrdersData && rawWorkOrdersData.data) ? rawWorkOrdersData.data : rawWorkOrdersData;
            function editWorkOrder(id) {
                const wo = workOrdersData.find(w => w.id === id);
                if (wo) {
                    document.getElementById('editStatus').value = wo.status;
                    document.getElementById('editCompletionQuantity').value = wo.completion_quantity ?? 0;
                }
                const modal = document.getElementById('editWorkOrderModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('editWorkOrderForm').action = `/production/${id}`;
                setTimeout(() => {
                    const content = modal.querySelector('.modal-content');
                    if (content) {
                        content.style.opacity = '0';
                        content.style.transform = 'scale(0.95) translateY(10px)';
                        setTimeout(() => {
                            content.style.transition = 'all 0.2s ease-out';
                            content.style.opacity = '1';
                            content.style.transform = 'scale(1) translateY(0)';
                        }, 10);
                    }
                }, 10);
            }

            function closeEditWorkOrderModal() {
                const modal = document.getElementById('editWorkOrderModal');
                const content = modal.querySelector('.modal-content');
                if (content) {
                    content.style.transition = 'all 0.15s ease-in';
                    content.style.opacity = '0';
                    content.style.transform = 'scale(0.95) translateY(10px)';
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 150);
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            function filterModalSalesOrders() {
                const searchTerm = document.getElementById('modalSalesOrderSearch').value.toLowerCase().trim();
                const cards = document.querySelectorAll('.sales-order-card');
                const noResults = document.getElementById('modalNoResults');
                let visibleCount = 0;

                cards.forEach(card => {
                    const orderNumber = card.getAttribute('data-order-number') || '';
                    const customerName = card.getAttribute('data-customer-name') || '';

                    if (orderNumber.includes(searchTerm) || customerName.includes(searchTerm)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
                }
            }

            // Store current items when sales order is selected (for product line dropdown)
            let currentOrderItems = [];

            function selectSalesOrder(salesOrderId, orderNumber, customerName, deliveryDate, items) {
                const listSection = document.getElementById('salesOrdersListSection');
                const formSection = document.getElementById('workOrderForm');
                const tableBody = document.getElementById('orderItemsTableBody');

                currentOrderItems = Array.isArray(items) ? items : JSON.parse(items);
                document.getElementById('formSalesOrderId').value = salesOrderId;
                document.getElementById('formDueDate').value = deliveryDate;
                document.getElementById('summaryOrderNumber').textContent = orderNumber;
                document.getElementById('summaryCustomer').textContent = customerName;
                document.getElementById('summaryDeliveryDate').textContent = deliveryDate ? new Date(deliveryDate).toLocaleDateString() : '-';

                // Populate items table
                tableBody.innerHTML = '';
                if (currentOrderItems.length > 0) {
                    currentOrderItems.forEach((item, idx) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="items[${idx}][selected]" value="1" class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked onchange="checkSelection()">
                                <input type="hidden" name="items[${idx}][product_id]" value="${item.id}">
                                <input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-medium">
                                ${item.name}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">
                                ${item.quantity}
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                    document.getElementById('selectAllItems').checked = true;
                } else {
                    tableBody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">No pending products found.</td></tr>';
                }

                listSection.classList.add('hidden');
                formSection.classList.remove('hidden');
                checkSelection();
            }

            function toggleAllItems(source) {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => cb.checked = source.checked);
                checkSelection();
            }

            function checkSelection() {
                const checkboxes = document.querySelectorAll('.item-checkbox:checked');
                const submitBtn = document.querySelector('#workOrderForm button[type="submit"]');
                const errorMsg = document.getElementById('itemSelectionError');
                
                if (checkboxes.length === 0) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    errorMsg.classList.remove('hidden');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    errorMsg.classList.add('hidden');
                }
            }
            
            function selectTeam(btn, teamName) {
                // Remove active state from all team buttons
                document.querySelectorAll('.team-btn').forEach(b => {
                    b.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
                    b.classList.add('border-gray-300', 'bg-white');
                });
                // Add active state to clicked button
                btn.classList.remove('border-gray-300', 'bg-white');
                btn.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
                // Set hidden input value
                document.getElementById('selectedTeam').value = teamName;
            }

            function resetWorkOrderForm() {
                const listSection = document.getElementById('salesOrdersListSection');
                const formSection = document.getElementById('workOrderForm');
                document.getElementById('workOrderForm').reset();
                // Reset team buttons
                document.querySelectorAll('.team-btn').forEach(b => {
                    b.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
                    b.classList.add('border-gray-300', 'bg-white');
                });
                document.getElementById('selectedTeam').value = '';
                listSection.classList.remove('hidden');
                formSection.classList.add('hidden');
            }

            function viewWorkOrder(id) {
                const wo = workOrdersData.find(w => w.id === id);
                if (!wo) {
                    console.warn('Work order not found in page data, attempting to fetch:', id);
                    // Fallback: try fetching from server
                    fetch(`/production/${id}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.workOrder) populateAndShowViewModal(data.workOrder);
                            else alert('Unable to load work order details.');
                        })
                        .catch(() => alert('Unable to load work order details.'));
                    return;
                }

                populateAndShowViewModal(wo);
            }

            function populateAndShowViewModal(wo) {
                currentWorkOrderId = wo.id;
                document.getElementById('vw_orderNumber').textContent = wo.order_number || wo.orderNumber || '-';
                document.getElementById('vw_productName').textContent = wo.product_name || wo.productName || '-';
                document.getElementById('vw_quantity').textContent = (wo.quantity !== undefined) ? (wo.quantity + ' pcs') : '-';
                document.getElementById('vw_startingDate').textContent = wo.starting_date ? new Date(wo.starting_date).toLocaleDateString() : (wo.startingDate ? new Date(wo.startingDate).toLocaleDateString() : 'Not started');
                document.getElementById('vw_dueDate').textContent = wo.due_date ? new Date(wo.due_date).toLocaleDateString() : (wo.dueDate || '-');
                document.getElementById('vw_assignedTo').textContent = wo.assigned_to || wo.assignedTo || '-';
                document.getElementById('vw_notes').value = wo.notes || wo.details || '';
                document.getElementById('notesForm').action = `/production/${wo.id}`;
                const modal = document.getElementById('viewWorkOrderModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeViewWorkOrderModal() {
                const modal = document.getElementById('viewWorkOrderModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Generic Modal Helpers
            function openModal(id) {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            let currentWorkOrderId = null;
            let currentCompleteOrderId = null;

            function openCompleteConfirmModal(id, orderNumber) {
                currentCompleteOrderId = id;
                document.getElementById('completeConfirmOrderNumber').textContent = orderNumber;
                const modal = document.getElementById('completeConfirmModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeCompleteConfirmModal() {
                const modal = document.getElementById('completeConfirmModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function confirmCompleteWorkOrder() {
                const orderNumber = document.getElementById('completeConfirmOrderNumber').textContent;
                
                fetch(`/production/${currentCompleteOrderId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    const isJson = contentType && contentType.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                        throw new Error(errorMessage);
                    }
                    return data;
                })
                .then(data => {
                    closeCompleteConfirmModal();
                    if (data.success) {
                        showSuccessNotification(`Work Order ${orderNumber} marked as completed!`);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showErrorNotification('Error completing work order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    closeCompleteConfirmModal();
                    showErrorNotification('Error completing work order: ' + error.message);
                });
            }

            function openCancelConfirmModal(id, orderNumber) {
                if (id) currentWorkOrderId = id;
                const displayNum = orderNumber || document.getElementById('vw_orderNumber').textContent;
                document.getElementById('cancelConfirmOrderNumber').textContent = displayNum;
                
                const modal = document.getElementById('cancelConfirmModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeCancelConfirmModal() {
                const modal = document.getElementById('cancelConfirmModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function confirmCancelWorkOrder() {
                const orderNumber = document.getElementById('vw_orderNumber').textContent;
                
                // Make the cancel request
                fetch(`/production/${currentWorkOrderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    const isJson = contentType && contentType.includes('application/json');
                    const data = isJson ? await response.json() : null;

                    if (!response.ok) {
                        const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                        throw new Error(errorMessage);
                    }
                    return data;
                })
                .then(data => {
                    closeCancelConfirmModal();
                    if (data.success) {
                        showSuccessNotification(`Work Order ${orderNumber} has been cancelled successfully.`);
                        closeViewWorkOrderModal();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showErrorNotification('Error cancelling work order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    closeCancelConfirmModal();
                    showErrorNotification('Error cancelling work order: ' + error.message);
                });
            }

            function showSuccessNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-green-100 border-2 border-green-400 text-green-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 4000);
            }

            function showErrorNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-red-100 border-2 border-red-400 text-red-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 5000);
            }

            // Production History Modal Functions
            function openProductionHistoryModal() {
                const modal = document.getElementById('productionHistoryModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    const content = modal.querySelector('.modal-content');
                    if (content) {
                        content.style.opacity = '0';
                        content.style.transform = 'scale(0.95) translateY(10px)';
                        setTimeout(() => {
                            content.style.transition = 'all 0.2s ease-out';
                            content.style.opacity = '1';
                            content.style.transform = 'scale(1) translateY(0)';
                        }, 10);
                    }
                }, 10);
            }

            function closeProductionHistoryModal() {
                const modal = document.getElementById('productionHistoryModal');
                const content = modal.querySelector('.modal-content');
                if (content) {
                    content.style.transition = 'all 0.15s ease-in';
                    content.style.opacity = '0';
                    content.style.transform = 'scale(0.95) translateY(10px)';
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 150);
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // Filter production history
            function filterProductionHistory() {
                const searchTerm = document.getElementById('historySearchInput')?.value.toLowerCase().trim() || '';
                const startDate = document.getElementById('historyStartDate')?.value || '';
                const endDate = document.getElementById('historyEndDate')?.value || '';
                
                const items = document.querySelectorAll('.history-item');
                const noResults = document.getElementById('historyNoResults');
                const emptyState = document.getElementById('historyEmptyState');
                let visibleCount = 0;
                const totalItems = items.length;

                items.forEach(item => {
                    const orderNumber = item.getAttribute('data-order-number') || '';
                    const productName = item.getAttribute('data-product-name') || '';
                    const assignedTo = item.getAttribute('data-assigned-to') || '';
                    const completedDate = item.getAttribute('data-completed-date') || '';
                    const startingDate = item.getAttribute('data-starting-date') || '';

                    // Search match
                    const searchMatch = !searchTerm || 
                        orderNumber.includes(searchTerm) || 
                        productName.includes(searchTerm) || 
                        assignedTo.includes(searchTerm);

                    // Date range match (check both completed and starting dates)
                    let dateMatch = true;
                    if (startDate || endDate) {
                        const checkDate = completedDate || startingDate;
                        if (checkDate) {
                            if (startDate && checkDate < startDate) dateMatch = false;
                            if (endDate && checkDate > endDate) dateMatch = false;
                        } else {
                            dateMatch = false;
                        }
                    }

                    // Show/hide item
                    if (searchMatch && dateMatch) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Update count badge
                const countBadge = document.getElementById('completedCount');
                if (countBadge) {
                    countBadge.textContent = `${visibleCount} of ${totalItems} Completed`;
                }

                // Show/hide no results message
                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount !== 0 || totalItems === 0);
                }
                if (emptyState) {
                    emptyState.classList.toggle('hidden', totalItems !== 0);
                }
            }

            function applyHistoryQuickFilter(type) {
                const startInput = document.getElementById('historyStartDate');
                const endInput = document.getElementById('historyEndDate');
                const now = new Date();
                const today = now.toISOString().split('T')[0];
                
                let start = '';
                let end = today;

                switch(type) {
                    case 'yesterday':
                        const yesterday = new Date(now);
                        yesterday.setDate(now.getDate() - 1);
                        start = yesterday.toISOString().split('T')[0];
                        end = start;
                        break;
                    case 'last-week':
                        const lastWeek = new Date(now);
                        lastWeek.setDate(now.getDate() - 7);
                        start = lastWeek.toISOString().split('T')[0];
                        break;
                    case '1-month':
                        const oneMonth = new Date(now);
                        oneMonth.setMonth(now.getMonth() - 1);
                        start = oneMonth.toISOString().split('T')[0];
                        break;
                    case '1-year':
                        const oneYear = new Date(now);
                        oneYear.setFullYear(now.getFullYear() - 1);
                        start = oneYear.toISOString().split('T')[0];
                        break;
                }

                if (startInput) startInput.value = start;
                if (endInput) endInput.value = end;
                filterProductionHistory();
            }

            // Clear history filters
            function clearHistoryFilters() {
                document.getElementById('historySearchInput').value = '';
                document.getElementById('historyStartDate').value = '';
                document.getElementById('historyEndDate').value = '';
                filterProductionHistory();
            }


            function startWorkOrder(id) {
                if (confirm('Start this work order?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/production/' + id + '/start';
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function pauseWorkOrder(id) {
                if (confirm('Pause this work order?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/production/' + id;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                    const method = document.createElement('input');
                    method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
                    const status = document.createElement('input');
                    status.type = 'hidden'; status.name = 'status'; status.value = 'in_progress';
                    form.appendChild(csrf); form.appendChild(method); form.appendChild(status);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function completeWorkOrder(id) {
                // Legacy function - now uses openCompleteConfirmModal
                // Kept for backward compatibility
            }

            // Close modals when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    closeWorkOrderModal();
                    closeEditWorkOrderModal();
                }
            });

            // Handle work order form submission with notification
            document.addEventListener('submit', function(e) {
                if (e.target.id === 'workOrderForm') {
                    e.preventDefault();
                    const form = e.target;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Create Work Order';
                    
                    if(submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
                    }

                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        const isJson = contentType && contentType.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            const errorMessage = (data && data.message) ? data.message : `HTTP error! status: ${response.status}`;
                            throw new Error(errorMessage);
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            showSuccessNotification(data.message);
                            closeWorkOrderModal();
                            form.reset();
                            
                            if (data.html) {
                                const tbody = document.getElementById('workOrderTableBody');
                                const emptyState = document.getElementById('workOrderEmptyState');
                                if (emptyState) emptyState.classList.add('hidden');
                                tbody.insertAdjacentHTML('afterbegin', data.html); // Add new row at top
                            } else {
                                setTimeout(() => location.reload(), 1500);
                            }
                        } else {
                            showErrorNotification(data.message || 'Error creating work order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorNotification('Error creating work order: ' + error.message);
                    })
                    .finally(() => {
                        if(submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                }
            });

            // Handle notes form submission with notification
            document.addEventListener('submit', function(e) {
                if (e.target.id === 'notesForm') {
                    e.preventDefault();
                    const form = e.target;
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: form.method === 'POST' ? 'POST' : 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server returned non-JSON response. Status: ' + response.status);
                        }
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showSuccessNotification('Notes saved successfully!');
                        } else {
                            showErrorNotification(data.message || 'Error saving notes');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorNotification('Error saving notes: ' + error.message);
                    });
                }
            });

            // Set minimum date to today and wire up search
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date().toISOString().split('T')[0];
                const dueDateInput = document.querySelector('input[name="due_date"]');
                if (dueDateInput) dueDateInput.setAttribute('min', today);

                const searchInput = document.getElementById('workOrderSearchInput');
                const statusFilter = document.getElementById('statusFilterSelect');

                // Combined filter function
                function filterWorkOrders() {
                    const searchTerm = searchInput?.value.toLowerCase().trim() || '';
                    const statusValue = statusFilter?.value || 'all';
                    const rows = document.querySelectorAll('.work-order-row');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const orderNumber = row.getAttribute('data-order-number')?.toLowerCase() || '';
                        const productName = row.getAttribute('data-product-name')?.toLowerCase() || '';
                        const assignedTo = row.getAttribute('data-assigned-to')?.toLowerCase() || '';
                        const status = row.getAttribute('data-status') || '';

                        // Check search match
                        const searchMatch = !searchTerm || 
                                          orderNumber.includes(searchTerm) || 
                                          productName.includes(searchTerm) || 
                                          assignedTo.includes(searchTerm);

                        // Check status match
                        const statusMatch = statusValue === 'all' || status === statusValue;

                        // Show row only if both conditions are met
                        if (searchMatch && statusMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Show/hide empty state
                    const emptyState = document.getElementById('workOrderEmptyState');
                    if (emptyState) {
                        emptyState.classList.toggle('hidden', visibleCount !== 0);
                    }
                }

                // Search functionality
                if (searchInput) {
                    searchInput.addEventListener('input', filterWorkOrders);
                }

                // Status filter functionality
                if (statusFilter) {
                    statusFilter.addEventListener('change', filterWorkOrders);
                }

                // Production History filters
                const historySearchInput = document.getElementById('historySearchInput');
                const historyStartDate = document.getElementById('historyStartDate');
                const historyEndDate = document.getElementById('historyEndDate');

                if (historySearchInput) {
                    historySearchInput.addEventListener('input', filterProductionHistory);
                }
                if (historyStartDate) {
                    historyStartDate.addEventListener('change', filterProductionHistory);
                }
                if (historyEndDate) {
                    historyEndDate.addEventListener('change', filterProductionHistory);
                }
            });
            // Toast Notification Functions
            function showSuccessNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-green-100 border-2 border-green-400 text-green-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 4000);
            }

            function showErrorNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[9999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-red-100 border-2 border-red-400 text-red-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 5000);
            }

            // Filter work orders by search text and status
            function filterWorkOrders() {
                const searchTerm = (document.getElementById('workOrderSearchInput').value || '').toLowerCase().trim();
                const statusFilter = document.getElementById('statusFilterSelect').value;
                const rows = document.querySelectorAll('.work-order-row');
                const emptyState = document.getElementById('workOrderEmptyState');
                let visibleCount = 0;

                rows.forEach(row => {
                    const orderNumber = (row.getAttribute('data-order-number') || '').toLowerCase();
                    const productName = (row.getAttribute('data-product-name') || '').toLowerCase();
                    const customerName = (row.getAttribute('data-customer-name') || '').toLowerCase();
                    const assignedTo = (row.getAttribute('data-assigned-to') || '').toLowerCase();
                    const status = row.getAttribute('data-status') || '';

                    const matchesSearch = !searchTerm ||
                        orderNumber.includes(searchTerm) ||
                        productName.includes(searchTerm) ||
                        customerName.includes(searchTerm) ||
                        assignedTo.includes(searchTerm);

                    const matchesStatus = statusFilter === 'all' || status === statusFilter;

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.classList.toggle('hidden', visibleCount > 0);
                    if (visibleCount === 0) {
                        emptyState.textContent = searchTerm ? 'No work orders match your search' : 'No active production found';
                    }
                }
            }

            @if(session('success'))
                document.addEventListener('DOMContentLoaded', function() {
                    showSuccessNotification('{{ session('success') }}');
                });
            @endif
            @if(session('error'))
                document.addEventListener('DOMContentLoaded', function() {
                    showErrorNotification('{{ session('error') }}');
                });
            @endif
        </script>

        <!-- Dynamic Sales Order Modals for Pending Items -->
        @foreach($pendingSalesOrders ?? [] as $order)
            @include('partials.sales-order-modals', [
                'order' => $order,
                'customerTypeBg' => $customerTypeBg,
                'statusBg' => $statusBg,
                'paymentBg' => $paymentBg
            ])
        @endforeach

</div>
@endsection