@extends('layouts.system')

@section('main-content')
    <!-- Main Content -->
    <style>
		.selected-row {
			background-color: #1e40af !important;
			color: white !important;
			border-left: 4px solid #f59e0b !important;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
		}

		/* New Dimmer Hover Effect */
		.data-row {
			cursor: pointer;
			transition: all 0.2s ease-in-out;
		}

		.data-row:hover {
			background-color: rgba(0, 0, 0, 0.2) !important;
			/* Dims the row */
			filter: brightness(1.1);
			/* Slightly pops the text */
		}

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

		.modal-overlay {
			display: none;
		}

		.modal-overlay.flex {
			display: flex;
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
    <div class="flex-1 flex flex-col overflow-hidden">
	<!-- Header Section -->
	<div class="p-5 bg-amber-50 border-b border-amber-200 relative z-30">
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-xl font-bold text-gray-800">Procurement Management</h1>
				<p class="text-xs font-medium text-gray-600 mt-2">Manage suppliers, purchase orders, and material sourcing</p>
			</div>
			@php
				$receiveCount = is_countable($openPurchaseOrders ?? $purchaseOrders ?? [])
					? count($openPurchaseOrders ?? $purchaseOrders ?? [])
					: 0;
			@endphp
			<div class="flex space-x-3 relative z-50">
				<!-- Export Button -->
				<div class="relative">
					<button id="exportButton" onclick="document.getElementById('exportDropdown').classList.toggle('hidden')" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-500 px-4 py-2 rounded-lg text-sm text-white transition">
						<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
						</svg>
						<span>Export</span>
						<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
						</svg>
					</button>
					<!-- Export Dropdown -->
					<div id="exportDropdown" class="hidden absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
						<div class="py-1">
							<a href="#" onclick="exportReceipt(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
									</svg>
									<span>Receipt</span>
								</div>
							</a>
						</div>
					</div>
				</div>
				
				<button onclick="openReceivedStockReportsModal()" class="px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-600 text-white rounded-lg hover:from-slate-600 hover:to-slate-500 transition-all shadow-lg hover:shadow-xl flex items-center gap-2 font-medium text-sm">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<rect x="3" y="13" width="4" height="8" rx="1" fill="currentColor" class="text-amber-300"/>
						<rect x="9" y="9" width="4" height="12" rx="1" fill="currentColor" class="text-amber-400"/>
						<rect x="15" y="5" width="4" height="16" rx="1" fill="currentColor" class="text-amber-500"/>
						<path stroke="currentColor" stroke-width="2" d="M3 21h18"/>
					</svg>
					<span>Reports</span>
				</button>
			</div>
		</div>
	</div>
    <div class="flex-1 p-5 bg-amber-50 overflow-y-auto">

		<!-- Metrics Cards -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 mb-6 px-5">
			<!-- Total Spent Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Total Spent</h3>
						@php
							$totalSpentFormatted = number_format($totalSpent, 0);
							$totalSpentLength = strlen($totalSpentFormatted);
							$totalSpentSize = $totalSpentLength > 15 ? 'text-lg' : ($totalSpentLength > 12 ? 'text-xl' : 'text-2xl');
						@endphp
						<p class="{{ $totalSpentSize }} font-bold mt-2 bg-gradient-to-r from-amber-300 to-amber-100 bg-clip-text text-transparent">â‚±{{ $totalSpentFormatted }}</p>
						<p class="text-slate-300 text-xs mt-1">All purchase orders</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
					  @include('components.icons.dollar', ['class' => 'w-5 h-5 text-amber-400'])
					</div>
				</div>
			</div>

			<!-- Payments Made Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Payments Made</h3>
						@php
							$paymentsMadeFormatted = number_format($paymentsMade, 0);
							$paymentsMadeLength = strlen($paymentsMadeFormatted);
							$paymentsMadeSize = $paymentsMadeLength > 15 ? 'text-lg' : ($paymentsMadeLength > 12 ? 'text-xl' : 'text-2xl');
						@endphp
						<p class="{{ $paymentsMadeSize }} font-bold mt-2 bg-gradient-to-r from-green-300 to-green-100 bg-clip-text text-transparent">â‚±{{ $paymentsMadeFormatted }}</p>
						<p class="text-slate-300 text-xs mt-1">Paid to suppliers</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.dollar', ['class' => 'w-5 h-5 text-green-400'])
					</div>
				</div>
			</div>

			<!-- Pending Payments Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Pending Payments</h3>
						@php
							$pendingPaymentsFormatted = number_format($pendingPayments, 0);
							$pendingPaymentsLength = strlen($pendingPaymentsFormatted);
							$pendingPaymentsSize = $pendingPaymentsLength > 15 ? 'text-lg' : ($pendingPaymentsLength > 12 ? 'text-xl' : 'text-2xl');
						@endphp
						<p class="{{ $pendingPaymentsSize }} font-bold mt-2 bg-gradient-to-r from-yellow-300 to-yellow-100 bg-clip-text text-transparent">â‚±{{ $pendingPaymentsFormatted }}</p>
						<p class="text-slate-300 text-xs mt-1">Outstanding amount</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						 @include('components.icons.dollar', ['class' => 'w-5 h-5 text-yellow-400'])
					</div>
				</div>
			</div>

			<!-- Active Suppliers Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Suppliers</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-blue-300 to-blue-100 bg-clip-text text-transparent">{{ $activeSuppliers }}</p>
						<p class="text-slate-300 text-xs mt-1">Suppliers</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
					  @include('components.icons.cart', ['class' => 'w-5 h-5 text-blue-400'])
					</div>
				</div>
			</div>

			<!-- Low Stock Alerts Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Low Stock Alerts</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-red-300 to-red-100 bg-clip-text text-transparent">{{ $lowStockAlerts }}</p>
						<p class="text-slate-300 text-xs mt-1">Items requiring attention</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.cart', ['class' => 'w-5 h-5 text-red-400'])
					</div>
				</div>
			</div>
		</div>

		<!-- Procurement Management Section -->
		<!-- Main Content Container (with Padding) -->
		<div class="px-5 pb-5">
			<section class="bg-gradient-to-br from-slate-700 to-slate-800 text-white p-3 rounded-xl overflow-visible shadow-xl border border-slate-600">
				<div class="flex justify-between items-center mb-6">
					<div>
						<h2 class="text-xl font-bold text-white">Procurement Management</h2>
						<p class="text-slate-300 text-xs font-medium mt-2">Manage purchase orders and supplier relationships</p>
					</div>
					<div id="purchaseOrdersButton" class="flex space-x-2">
						<button onclick="openAddPurchaseOrderModal()" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-1.5 font-medium">
							<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
							</svg>
							<span>New Purchase Order</span>
						</button>
                        <button type="button" onclick="openArchiveModal()" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-500 px-4 py-2 rounded-lg text-sm text-white transition shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span>Order History</span>
                        </button>
					</div>
					<div id="suppliersButton" class="flex space-x-2 hidden">
						<button onclick="openAddSupplierModal()" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-1.5 font-medium">
							<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
							</svg>
							<span>New Supplier</span>
						</button>
					</div>
				</div>

				<!-- Search and Filter Bar -->
				<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
					<div class="flex-1 max-w-md w-full">
						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
								</svg>
							</div>
							<input id="searchInput" type="text" placeholder="Search orders or suppliers..." class="w-full pl-10 pr-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400">
						</div>
					</div>

					<div class="flex items-center space-x-2 w-full md:w-auto">
						<select id="statusFilter" class="bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 w-full md:w-auto">
							<option value="all">All Status</option>
							<option value="paid">Paid</option>
							<option value="partial">Partial</option>
							<option value="pending">Pending</option>
						</select>
					</div>
				</div>

				<!-- Tabs -->
				<div class="flex space-x-2 w-full mb-6">
					<button onclick="showTab('purchase-orders')" id="purchase-orders-tab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #FFF1DA; border-color: #FDE68A; color: #111827;">Orders</button>
					<button onclick="showTab('suppliers')" id="suppliers-tab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #475569; border-color: #64748b; color: #FFFFFF;">Suppliers</button>
				</div>

				<!-- Purchase Orders Table -->
				<div id="purchase-orders-table" class="overflow-x-auto rounded-xl border border-slate-600 custom-scrollbar" style="max-height: 60vh;">
					<table class="w-full table-fixed text-left border-collapse">
						<thead >
							<tr class="bg-slate-700 text-slate-300 border-b border-slate-600 sticky top-0 z-10">
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Order #</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Supplier</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Order Date</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Expected Delivery</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Total Amount</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Payment Status</th>
								<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Action</th>
							</tr>
						</thead>
						<tbody id="procurementTbody" class="divide-y divide-slate-600">
							@forelse($purchaseOrders ?? [] as $order)
							<tr class="border-b border-slate-600 hover:bg-slate-700/50 transition-colors duration-200 cursor-pointer data-row">
								<td class="px-4 py-3 font-mono text-slate-300">{{ $order->order_number ?? 'PO-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
								<td class="px-4 py-3 text-slate-300 font-medium">{{ $order->supplier->name }}</td>
								<td class="px-4 py-3 text-slate-300">{{ $order->order_date->format('M d, Y') }}</td>
								<td class="px-4 py-3 text-slate-300">{{ $order->expected_delivery->format('M d, Y') }}</td>
								<td class="px-4 py-3 font-bold text-slate-300">â‚±{{ number_format($order->total_amount, 2) }}</td>
								<td class="px-4 py-3">
									@if($order->payment_status === 'Paid')
										<span class="text-xs font-bold text-green-400">{{ $order->payment_status }}</span>
									@elseif($order->payment_status === 'Partial')
										<span class="text-xs font-bold text-amber-400">{{ $order->payment_status }}</span>
									@else
										<span class="text-xs font-bold text-slate-400">Pending</span>
									@endif
								</td>
								<td class="py-3 px-4">
									<div class="flex space-x-2 justify-start">
										<button onclick="event.stopPropagation(); openViewOrderModal({{ $order->id }})" class="p-1 hover:bg-slate-500 rounded text-slate-400 hover:text-white transition-colors" title="View Items">
											<svg class="w-4 h-4" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none">
                                                <path d="M53.79,33.1a.51.51,0,0,0,0-.4C52.83,30.89,45.29,17.17,32,16.84S11,30.61,9.92,32.65a.48.48,0,0,0,0,.48C11.1,35.06,19.35,48.05,29.68,49,41.07,50,50.31,42,53.79,33.1Z"/>
                                                <circle cx="31.7" cy="32.76" r="6.91"/>
                                            </svg>
										</button>
										<button onclick="event.stopPropagation(); editOrder({{ $order->id }})" class="p-1 hover:bg-slate-500 rounded text-slate-400 hover:text-amber-400 transition-colors">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
										</button>
										@if($order->payment_status !== 'Paid' && $order->payment_status !== 'Partial')
										<button onclick="event.stopPropagation(); deleteOrder({{ $order->id }})" class="p-1 hover:bg-slate-500 rounded text-slate-400 hover:text-red-400 transition-colors" title="Cancel Order">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
										</button>
										@endif

									</div>
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="7" class="py-8 px-4 text-center text-slate-400">No purchase orders found</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>

            <!-- Suppliers Table -->
            <div id="suppliers-table" class="overflow-y-auto hidden custom-scrollbar" style="max-height: 60vh;">
                <table class="w-full table-fixed border-collapse text-left text-xs text-white">
                    <thead >
                        <tr class="bg-slate-700 text-slate-300 border-b border-slate-600 sticky top-0 z-10">
                            <th class="px-3 py-3 font-medium rounded-tl-xl">Name</th>
                            <th class="px-3 py-3 font-medium">Contact Person</th>
                            <th class="px-3 py-3 font-medium">Phone</th>
                            <th class="px-3 py-3 font-medium">Email</th>
                            <th class="px-3 py-3 font-medium">Payment Terms</th>
                            <th class="px-3 py-3 font-medium text-center rounded-tr-xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-600">
                        @forelse($suppliers ?? [] as $supplier)
                        <tr class="hover:bg-slate-600 transition cursor-pointer data-row" onclick="selectRow(this)">
                            <td class="px-3 py-3 font-medium text-slate-300 border-l-4 border-amber-500">{{ $supplier->name }}</td>
                            <td class="px-3 py-3 text-slate-300">{{ $supplier->contact_person }}</td>
                            <td class="px-3 py-3 text-slate-300">{{ $supplier->phone }}</td>
                            <td class="px-3 py-3 text-slate-300">{{ $supplier->email }}</td>
                            <td class="px-3 py-3 text-slate-300">{{ $supplier->payment_terms }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="event.stopPropagation(); editSupplier({{ $supplier->id }})" class="p-1.5 hover:bg-slate-500 rounded text-slate-400 hover:text-amber-400 transition-colors" title="Edit Supplier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); deleteSupplier({{ $supplier->id }})" class="p-1.5 hover:bg-slate-500 rounded text-slate-400 hover:text-red-400 transition-colors" title="Delete Supplier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-slate-400">No suppliers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-3">
            <div class="bg-amber-50 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
                <!-- Header -->
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-2xl z-10">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-1.5">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Add New Supplier
                            </h3>
                            <p class="text-slate-300 text-xs mt-1">Register a new supplier to the system</p>
                        </div>
                        <button onclick="closeAddSupplierModal()" class="text-white hover:text-slate-300 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mx-6 mt-6 p-3 bg-red-50 border-2 border-red-400 rounded-xl shadow-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-red-800 font-bold text-base mb-2">Please fix the following errors:</h4>
                                <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Content -->
                <div class="p-3">
                    <form id="addSupplierForm" method="POST" action="{{ route('procurement.supplier.store') }}" onsubmit="return confirmSupplier(event)">
                        @csrf
                        <div class="space-y-5">
                            <!-- Basic Information Section -->
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                                    <span class="w-1 h-6 bg-amber-500 rounded"></span>
                                    Basic Information
                                </h4>

                                <div class="space-y-4">
                                    <!-- Supplier Name -->
                                    <div>
                                        <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                            </svg>
                                            Supplier Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" required placeholder="Enter supplier company name">
                                        @error('name')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Contact Person -->
                                    <div>
                                        <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            Contact Person <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="contact_person" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('contact_person') border-red-500 @enderror" value="{{ old('contact_person') }}" required placeholder="Enter contact person name">
                                        @error('contact_person')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Payment Terms -->
                                    <div>
                                        <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                            </svg>
                                            Payment Terms <span class="text-red-500">*</span>
                                        </label>
                                        <select name="payment_terms" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('payment_terms') border-red-500 @enderror" required>
                                            <option value="">-- Select Payment Terms --</option>
                                            <option value="Net 15" {{ old('payment_terms') == 'Net 15' ? 'selected' : '' }}>Net 15 Days</option>
                                            <option value="Net 30" {{ old('payment_terms') == 'Net 30' ? 'selected' : '' }}>Net 30 Days</option>
                                            <option value="Net 45" {{ old('payment_terms') == 'Net 45' ? 'selected' : '' }}>Net 45 Days</option>
                                            <option value="Net 60" {{ old('payment_terms') == 'Net 60' ? 'selected' : '' }}>Net 60 Days</option>
                                            <option value="Cash on Delivery" {{ old('payment_terms') == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                        </select>
                                        @error('payment_terms')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="pt-4 border-t-2 border-gray-300">
                                <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                                    <span class="w-1 h-6 bg-amber-500 rounded"></span>
                                    Contact Information
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Phone -->
                                    <div>
                                        <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="phone" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('phone') border-red-500 @enderror" value="{{ old('phone') }}" required placeholder="09XXXXXXXXX">
                                        @error('phone')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('email') border-red-500 @enderror" value="{{ old('email') }}" required placeholder="supplier@example.com">
                                        @error('email')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="pt-4 border-t-2 border-gray-300">
                                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <textarea name="address" rows="3" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('address') border-red-500 @enderror" required placeholder="Enter supplier's complete address">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">Required fields:</span> All fields are required
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-base hover:bg-gray-100 transition-all shadow-sm" onclick="closeAddSupplierModal()">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-base rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Supplier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Purchase Order Modal -->
    <div id="addPurchaseOrderModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-3">
            <div class="bg-amber-50 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
                <!-- Header -->
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-2xl z-10">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-1.5">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Create Purchase Order
                            </h3>
                            <p class="text-slate-300 text-xs mt-1">Add a new purchase order from supplier</p>
                        </div>
                        <button onclick="closeAddPurchaseOrderModal()" class="text-white hover:text-slate-300 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mx-6 mt-6 p-3 bg-red-50 border-2 border-red-400 rounded-xl shadow-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-red-800 font-bold text-base mb-2">Please fix the following errors:</h4>
                                <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Content -->
                <div class="p-3">
                    <form id="addPurchaseOrderForm" method="POST" action="{{ route('procurement.purchase-order.store') }}" onsubmit="return confirmPurchaseOrder(event)">
                        @csrf
                        <div class="space-y-5">
                            <!-- Supplier Selection (Searchable) -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <input type="hidden" name="supplier_id" id="procSupplierIdHidden" value="{{ old('supplier_id') }}" required>
                                <div class="relative" id="supplierSearchWrapper">
                                    <input type="text" id="supplierSearchInput" autocomplete="off" placeholder="Type to search supplier name..." class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('supplier_id') border-red-500 @enderror" oninput="filterSupplierList()" onfocus="document.getElementById('supplierResultsList').classList.remove('hidden')">
                                    <!-- Selected supplier badge -->
                                    <div id="supplierSelectedBadge" class="hidden mt-2 p-2.5 bg-amber-50 border-2 border-amber-400 rounded-xl flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm" id="supplierInitial">?</div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm" id="supplierSelectedName">â€”</p>
                                                <p class="text-xs text-gray-500" id="supplierSelectedInfo">â€”</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearSupplierSelection()" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg p-1 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <!-- Results dropdown -->
                                    <div id="supplierResultsList" class="hidden absolute z-[100001] w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-2xl max-h-48 overflow-y-auto">
                                        @foreach($suppliers ?? [] as $supplier)
                                        <button type="button" class="supplier-option w-full flex items-center gap-3 px-3 py-2.5 hover:bg-amber-50 transition-all text-left border-b border-gray-100 last:border-b-0" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-contact="{{ $supplier->contact_person }}" onclick="selectSupplier(this)">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-bold text-gray-900 text-sm truncate">{{ $supplier->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $supplier->contact_person }}</p>
                                            </div>
                                        </button>
                                        @endforeach
                                        <div id="supplierNoMatch" class="hidden px-4 py-3 text-center text-gray-400 text-sm">No matching supplier found</div>
                                    </div>
                                </div>
                                @error('supplier_id')
                                    <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Order Date -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Order Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="order_date" min="{{ date('Y-m-d') }}" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('order_date') border-red-500 @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')
                                    <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Expected Delivery -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    Expected Delivery <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="expected_delivery" min="{{ date('Y-m-d') }}" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('expected_delivery') border-red-500 @enderror" value="{{ old('expected_delivery') }}" required>
                                @error('expected_delivery')
                                    <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Material Selection Section -->
                            <div class="pt-4 border-t-2 border-gray-300">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-bold text-gray-900 flex items-center gap-1.5">
                                        <span class="w-1 h-6 bg-amber-500 rounded"></span>
                                        Order Items
                                    </h4>
                                    <button type="button" onclick="addProcurementMaterialRow()" class="px-3 py-2 bg-green-600 text-white text-xs rounded-xl hover:bg-green-700 font-bold shadow-lg transition-all flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                        </svg>
                                         Add Material
                                    </button>
                                </div>
                                
                                <div id="procurementItemsContainer">
                                    <!-- Item Row 0 -->
                                    <div class="bg-white rounded-xl border-2 border-slate-300 p-5 shadow-lg mb-3 procurement-item-row" data-index="0">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <!-- Material Selection (Searchable) -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                    </svg>
                                                    Material <span class="text-red-500">*</span>
                                                </label>
                                                <!-- Hidden select kept for pricing JS compatibility -->
                                                <select id="newItemMaterial" name="items[0][material_id]" class="hidden proc-material-select" required>
                                                    <option value="">-- Select Material --</option>
                                                    @foreach($materials ?? [] as $material)
                                                        <option value="{{ $material->id }}" data-price="{{ number_format($material->unit_cost,2,'.','') }}">{{ $material->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="relative proc-material-search-wrapper" id="materialSearchWrapper">
                                                    <input type="text" id="materialSearchInput" autocomplete="off" placeholder="Search material..." class="w-full border-2 border-gray-300 rounded-lg px-3 py-1.5 text-base focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all proc-material-search @error('items.0.material_id') border-red-500 @enderror" oninput="filterProcMaterialList(this)" onfocus="this.closest('.proc-material-search-wrapper').querySelector('.proc-material-results').classList.remove('hidden')">
                                                    <!-- Selected material badge -->
                                                    <div class="hidden mt-1.5 p-2 bg-amber-50 border-2 border-amber-400 rounded-lg flex items-center justify-between proc-material-badge">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-xs proc-material-initial">?</div>
                                                            <div>
                                                                <p class="font-bold text-gray-900 text-xs proc-material-selected-name">â€”</p>
                                                                <p class="text-[10px] text-gray-500 proc-material-selected-price">â€”</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" onclick="clearProcMaterialSelection(this)" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg p-0.5 transition-all">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <!-- Results dropdown -->
                                                    <div class="hidden absolute z-[100001] w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-2xl max-h-44 overflow-y-auto proc-material-results" id="materialResultsList">
                                                        @foreach($materials ?? [] as $material)
                                                        <button type="button" class="proc-material-option w-full flex items-center gap-2.5 px-3 py-2 hover:bg-amber-50 transition-all text-left border-b border-gray-100 last:border-b-0" data-id="{{ $material->id }}" data-name="{{ $material->name }}" data-price="{{ number_format($material->unit_cost,2,'.','') }}" onclick="selectProcMaterial(this)">
                                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">
                                                                {{ strtoupper(substr($material->name, 0, 1)) }}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="font-bold text-gray-900 text-xs truncate">{{ $material->name }}</p>
                                                                <p class="text-[10px] text-gray-500">â‚±{{ number_format($material->unit_cost, 2) }}</p>
                                                            </div>
                                                        </button>
                                                        @endforeach
                                                        <div class="proc-material-no-match hidden px-4 py-3 text-center text-gray-400 text-sm">No matching material found</div>
                                                    </div>
                                                </div>
                                                @error('items.0.material_id')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Quantity -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                    </svg>
                                                    Quantity <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" min="1" step="1" name="items[0][quantity]" class="w-full border-2 border-gray-300 rounded-lg px-3 py-1.5 text-base font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all proc-item-qty @error('items.0.quantity') border-red-500 @enderror" placeholder="1" value="{{ old('items.0.quantity') }}" required oninput="updateProcRowTotal(this)">
                                                @error('items.0.quantity')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Unit Price -->
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                                    </svg>
                                                    Unit Price
                                                </label>
                                                <input type="text" class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-base font-bold bg-gray-100 text-gray-600 proc-item-unit-price" value="" placeholder="Auto-filled" disabled>
                                                <input type="hidden" name="items[0][unit_price]" class="proc-item-unit-price-hidden" value="">
                                            </div>
                                        </div>

                                        <!-- Line Total Display -->
                                        <div class="mt-4 p-3 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Line Total:
                                                </span>
                                                <span class="text-xl font-bold text-amber-700 proc-item-line-total">â‚±0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grand Total Display -->
                                <div class="mt-2 p-4 bg-gradient-to-r from-slate-700 to-slate-800 border-2 border-slate-600 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-white flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            Order Grand Total:
                                        </span>
                                        <span id="procGrandTotal" class="text-2xl font-bold text-amber-400">â‚±0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div>
                                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Additional Notes (Optional)
                                </label>
                                <textarea name="note" rows="3" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Add any special instructions or notes for this order..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">Required fields:</span> Supplier, Order Date, Expected Delivery, Material
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-base hover:bg-gray-100 transition-all shadow-sm" onclick="closeAddPurchaseOrderModal()">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-base rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create Purchase Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Order Items Modal -->
    <div id="viewOrderItemsModal" class="fixed inset-0 bg-black/70 hidden overflow-y-auto" style="z-index: 99999; cursor: default;" onclick="if(event.target === this) closeViewOrderItemsModal()">
        <div class="rounded-lg shadow-2xl max-w-4xl w-[95%] mx-auto my-8 p-5" style="background-color: #FFF1DA;" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between mb-5 border-b-2 pb-6" style="border-color: #374151;">
                <div>
                    <h3 class="text-xl font-bold flex items-center gap-2" id="viewOrderHeaderNumber" style="color: #374151;">
                        Order #-
                    </h3>
                    <p class="mt-1" id="viewOrderHeaderSupplier" style="color: #666;">-</p>
                </div>
                <button class="text-xl transition" style="color: #999;" onclick="closeViewOrderItemsModal()">âœ•</button>
            </div>

            <!-- Content -->
            <div class="p-0">
                <!-- Order Information Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-3 rounded-lg border-l-4 shadow-sm" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                        <p class="text-xs font-bold text-gray-500 uppercase">Order Number</p>
                        <p class="text-lg font-bold mt-1 text-gray-900" id="viewOrderDetailNumber">-</p>
                    </div>
                    <div class="p-3 rounded-lg border-l-4 shadow-sm" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                        <p class="text-xs font-bold text-gray-500 uppercase">Supplier</p>
                        <p class="text-lg font-bold mt-1 text-gray-900" id="viewOrderDetailSupplier">-</p>
                    </div>
                    <div class="p-3 rounded-lg border-l-4 shadow-sm" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                        <p class="text-xs font-bold text-gray-500 uppercase">Total Items</p>
                        <p class="text-lg font-bold mt-1 text-gray-900" id="viewOrderTotalItems">0</p>
                    </div>
                    <div class="p-3 rounded-lg border-l-4 shadow-sm" style="background-color: rgba(255,255,255,0.7); border-left-color: #10B981;">
                        <p class="text-xs font-bold text-green-600 uppercase">Order Total</p>
                        <p class="text-lg font-bold mt-1 text-green-700" id="viewOrderTotalAmount">â‚±0.00</p>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-2">
                    <h4 class="text-xl font-bold mb-4 flex items-center" style="color: #374151;">
                        <span class="w-1 h-6 rounded mr-3" style="background-color: #374151;"></span>
                        Materials & Items Ordered
                    </h4>
                    <div id="viewOrderItemsContainer" class="space-y-3">
                        <div class="p-8 rounded-xl border-2 border-dashed border-slate-300 text-center text-gray-500">
                            Loading items...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end mt-6">
                <button class="px-6 py-3 rounded-lg font-semibold transition text-white" style="background-color: #374151;" onclick="closeViewOrderItemsModal()">Close Details</button>
            </div>
        </div>
    </div>

    <!-- Edit Purchase Order Modal -->
    <div id="editPurchaseOrderModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50" style="align-items: center; justify-content: center;">
        <div class="p-4 w-full max-w-2xl">
            <div class="bg-amber-50 rounded-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                        <h3 class="text-xl font-bold text-gray-900">Edit Purchase Order</h3>
                        <button onclick="closeEditPurchaseOrderModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="editPurchaseOrderForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Order Number</label>
                                <input type="text" id="editPOOrderNumber" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 bg-gray-100 text-sm transition-all" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Supplier</label>
                                <input type="text" id="editPOSupplierName" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 bg-gray-100 text-sm transition-all" readonly>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Expected Delivery</label>
                                    <input type="date" id="editPOExpectedDelivery" name="expected_delivery" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 text-sm transition-all" />
                                </div>
                            </div>
                          
                            <div class="flex justify-end space-x-2 mt-6">
                                <button type="button" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all" onclick="closeEditPurchaseOrderModal()">Cancel</button>
                                <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">Update Order</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div id="editSupplierModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-amber-50 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                        <h3 class="text-xl font-bold text-gray-900">Edit Supplier</h3>
                        <button onclick="closeEditSupplierModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="editSupplierForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Name *</label>
                                <input type="text" id="editSupplierName" name="name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Contact Person *</label>
                                <input type="text" id="editSupplierContactPerson" name="contact_person" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Phone *</label>
                                <input type="text" id="editSupplierPhone" name="phone" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Email *</label>
                                <input type="email" id="editSupplierEmail" name="email" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-900 mb-3">Address *</label>
                                <textarea id="editSupplierAddress" name="address" rows="2" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Payment Terms *</label>
                                <input type="text" id="editSupplierPaymentTerms" name="payment_terms" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Status *</label>
                                <select id="editSupplierStatus" name="status" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all" onclick="closeEditSupplierModal()">Cancel</button>
                            <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">Update Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Purchase Order -->
    <div id="confirmPurchaseOrderModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001]" onclick="if(event.target === this) closeConfirmPurchaseOrder()">
        <div class="flex items-center justify-center min-h-screen p-3 w-full">
            <div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700 z-[100003]">
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-0xl z-[100002]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Confirm Purchase Order</h3>
                        <button onclick="closeConfirmPurchaseOrder()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate-700 mb-6">Create this new purchase order? Please review all details before confirming.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeConfirmPurchaseOrder()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Review Again</button>
                        <button type="button" onclick="submitPurchaseOrder()" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Supplier -->
    <div id="confirmSupplierModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001]" onclick="if(event.target === this) closeConfirmSupplier()">
        <div class="flex items-center justify-center min-h-screen p-3 w-full">
            <div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700 z-[100003]">
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-xl z-[100002]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Add New Supplier</h3>
                        <button onclick="closeConfirmSupplier()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate-700 mb-6">Register this new supplier to the system? Their information will be saved for future orders.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeConfirmSupplier()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Cancel</button>
                        <button type="button" onclick="submitSupplier()" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Purchase Order Confirmation Modal -->
    <div id="deleteOrderModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001]" onclick="if(event.target === this) closeDeleteOrder()">
        <div class="flex items-center justify-center min-h-screen p-3 w-full">
            <div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700 z-[100003]">
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-xl z-[100002]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Cancel Purchase Order</h3>
                        <button onclick="closeDeleteOrder()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate-700 mb-6">Cancel this purchase order? This action cannot be undone.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteOrder()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Keep It</button>
                        <button type="button" onclick="submitDeleteOrder()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Supplier Confirmation Modal -->
    <div id="deleteSupplierModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001]" onclick="if(event.target === this) closeDeleteSupplier()">
        <div class="flex items-center justify-center min-h-screen p-3 w-full">
            <div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700 z-[100003]">
                <div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-xl z-[100002]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Delete Supplier</h3>
                        <button onclick="closeDeleteSupplier()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate-700 mb-6">Delete this supplier? This action cannot be undone.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteSupplier()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Keep</button>
                        <button type="button" onclick="submitDeleteSupplier()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Improved Received Stock Reports Modal -->
    <div id="receivedStockReportsModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center p-3 z-[99999]" style="cursor: default;" onclick="if(event.target === this) closeReceivedStockReportsModal()">
        <div class="rounded-xl shadow-2xl w-full mx-auto p-0 overflow-hidden border-2 border-slate-700 flex flex-col" style="background-color: #FFF1DA; max-width: 98vw; height: 95vh;" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-5 border-b-2 shrink-0" style="border-color: #374151;">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg" style="background-color: rgba(55, 65, 81, 0.1);">
                        <svg class="w-6 h-6" style="color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold" style="color: #374151;">Procurement Reports</h3>
                        <p class="text-xs mt-0.5" style="color: #666;">Track and manage incoming inventory</p>
                    </div>
                </div>
                <button onclick="closeReceivedStockReportsModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-xl p-2 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

                <div class="px-5 pt-4 pb-2">
                    <!-- Search Bar -->
                    <input type="search" id="reportSearchInput" placeholder="Search reports..." class="bg-white w-full rounded-lg px-4 py-2 text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50 border border-gray-300 mb-3 transition-all">
                    
                    <!-- Date Range Filters -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">From</label>
                            <input type="date" id="filterDateFrom" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">To</label>
                            <input type="date" id="filterDateTo" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
                        </div>
                    </div>

                    <!-- Quick Filters -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        <button onclick="applyReportQuickFilter('yesterday')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Yesterday</button>
                        <button onclick="applyReportQuickFilter('last-week')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Last Week</button>
                        <button onclick="applyReportQuickFilter('1-month')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Month</button>
                        <button onclick="applyReportQuickFilter('1-year')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Year</button>
                    </div>

                    <!-- Actions Row -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2">
                             <button onclick="filterReceivedStock()" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-amber-500 text-white shadow-lg hover:bg-amber-600">Apply Filters</button>
                             <button onclick="resetFilters()" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-all text-xs font-bold">Reset</button>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Excel
                            </button>
                            <button onclick="printReport()" class="bg-slate-600 hover:bg-slate-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="px-5 pb-4 grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg border-l-4 shadow-sm flex justify-between items-center" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-500">Total Received</p>
                            <p id="totalReceived" class="text-2xl font-bold mt-1" style="color: #374151;">0</p>
                        </div>
                        <div class="p-2 rounded-lg shadow-inner" style="background-color: rgba(55, 65, 81, 0.05);">
                             <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                             </svg>
                        </div>
                    </div>
                    <div class="p-4 rounded-lg border-l-4 shadow-sm flex justify-between items-center" style="background-color: rgba(255,255,255,0.7); border-left-color: #EF4444;">
                        <div>
                             <p class="text-[10px] uppercase font-bold text-red-500">Total Defects</p>
                             <p id="totalDefects" class="text-2xl font-bold mt-1 text-red-700">0</p>
                        </div>
                        <div class="p-2 rounded-lg shadow-inner" style="background-color: rgba(239, 68, 68, 0.05);">
                             <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                             </svg>
                        </div>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="px-5 pb-3 overflow-y-auto custom-scrollbar flex-1 min-h-0">
                    <table class="w-full border-separate border-spacing-y-2 text-left text-xs">
                        <thead class="text-gray-700 bg-gray-100/80 sticky top-0 z-10">
                            <tr>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold rounded-tl-xl">Date</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold">PO Number</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold">Material</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold">Qty</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold text-red-600">Defects</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold">Supplier</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold">Status</th>
                                <th class="px-2 py-3 sm:px-4 text-[10px] sm:text-xs font-bold rounded-tr-xl">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="receivedStockTableBody" class="text-gray-600">
                            <tr>
                                <td colspan="8" class="py-12 px-4">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-16 h-16 mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <p class="text-lg font-medium">Loading data...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-5 py-3 border-t-2 flex justify-between items-center" style="border-color: #374151;">
                    <div class="text-xs text-gray-600">
                        Showing <span class="font-bold" id="showingFrom">0</span> to 
                        <span class="font-bold" id="showingTo">0</span> of 
                        <span class="font-bold" id="totalRecords">0</span> results
                    </div>
                    <div class="flex gap-2">
                        <button onclick="previousPage()" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                        <button onclick="nextPage()" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal Animation Styles -->
    <style>
    /* Modal entrance animation */
    #receivedStockReportsModal:not(.hidden) .modal-content {
        animation: modalSlideIn 0.3s ease-out forwards;
    }

    @keyframes modalSlideIn {
        from {
            transform: scale(0.95) translateY(-20px);
            opacity: 0;
        }
        to {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    /* Table row hover effect */
    #receivedStockTableBody tr:hover {
        background-color: #f8fafc;
        transition: background-color 0.2s ease;
    }

    /* Smooth transitions for all interactive elements */
    button, input, select {
        transition: all 0.2s ease;
    }

    /* Custom scrollbar for the modal */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>



    <script>
        window.__procurementData = {
            purchaseOrders: @json($purchaseOrders ?? []),
            suppliers: @json($suppliers ?? []),
            materials: @json($materials ?? [])
        };
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessNotification('{{ session("success") }}');
            });
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                showErrorNotification('{{ session("error") }}');
            });
        @endif
    </script>
    <script src="{{ asset('js/procurement.js') }}?v={{ filemtime(public_path('js/procurement.js')) }}"></script>
</div>
@endsection
