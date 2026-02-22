@extends('layouts.system')

@section('main-content')
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
@php
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
@endphp
<!-- Main Content -->
<div class="flex-1 flex flex-col overflow-auto">
	<!-- Success Notification -->
	<div id="successNotification" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[999999] bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg text-lg font-semibold flex items-center gap-2 hidden transition-all duration-300">
		<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
		</svg>
		<span id="successNotificationText">Success!</span>
	</div>
	<!-- Header -->
	<div class="bg-amber-50 p-5 relative z-10 border-b border-amber-200">
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-xl font-bold text-gray-800">Sales & Orders</h1>
				<p class="text-base text-gray-600 mt-2">Manage customer orders, sales, and deliveries</p>
			</div>
			<div class="flex items-center gap-3 relative z-50">
				<!-- Export Button -->
				<div class="relative">
					<button id="exportButton" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-500 px-4 py-2 rounded-lg text-sm text-white transition">
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
							<a href="#" onclick="exportSalesReport(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
									</svg>
									<span>Sales Report</span>
								</div>
							</a>
							<a href="#" onclick="exportCustomerList(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<span>Customer List</span>
								</div>
							</a>
						</div>
					</div>
				</div>

			


		</div>
	</div>

	<!-- Dashboard Content -->
	<div class="flex-1 p-5 bg-amber-50 overflow-visible min-h-0 custom-scrollbar">
		<!-- Summary Cards Row -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
			<!-- Total Revenue Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Total Revenue</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-amber-300 to-amber-100 bg-clip-text text-transparent">₱{{ number_format($salesOrders->sum('total_amount'), 0) }}</p>
						<p class="text-slate-300 text-xs mt-1">All time sales</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.package', ['class' => 'w-5 h-5 text-amber-400'])
					</div>
				</div>
			</div>

			<!-- Payments Received Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Payments Received</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-green-300 to-green-100 bg-clip-text text-transparent">₱{{ number_format($salesOrders->where('payment_status', 'Paid')->sum('total_amount'), 0) }}</p>
						<p class="text-slate-300 text-xs mt-1">Paid orders</p>
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
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-yellow-300 to-yellow-100 bg-clip-text text-transparent">₱{{ number_format($salesOrders->whereIn('payment_status', ['Pending', 'Partial'])->sum('total_amount'), 0) }}</p>
						<p class="text-slate-300 text-xs mt-1">Outstanding amount</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.dollar', ['class' => 'w-5 h-5 text-yellow-400'])
					</div>
				</div>
			</div>

			<!-- Active Orders Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Active Orders</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-blue-300 to-blue-100 bg-clip-text text-transparent">{{ $salesOrders->whereIn('status', ['Pending', 'In production', 'Ready'])->count() }}</p>
						<p class="text-slate-300 text-xs mt-1">Orders in progress</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.cart', ['class' => 'w-5 h-5 text-blue-400'])
					</div>
				</div>
			</div>
		</div>

		<!-- Sales Management Card -->
		<section class="bg-gradient-to-br from-slate-700 to-slate-800 text-white p-3 rounded-xl overflow-visible shadow-xl border border-slate-600">
			<header class="flex justify-between items-center mb-6">
				<div>
					<h2 class="text-xl font-bold text-white">Sales Management</h2>
					<p class="text-slate-300 text-xs font-medium mt-2">Manage customer orders and track sales performance</p>
				</div>
				<div class="flex items-center gap-2">
					<button id="headerBtn" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-1.5 font-medium" onclick="openModal('newOrderModal')">
						<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
						</svg>
						<span>New Order</span>
					</button>
						<!-- Archive Button -->
				<button type="button" onclick="openModal('archiveModal')" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-500 px-4 py-2 rounded-lg text-sm text-white transition shadow-sm hover:shadow-md">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
					</svg>
					<span>Order History</span>
				</button>
			
				</div>
			</header>

			<!-- Search + Filters -->
			<!-- Search + Filters -->
			<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
				<div class="flex-1 max-w-md">
					<div class="relative">
						<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
						</div>
						<input type="search" id="searchInput" placeholder="Search order or customers..." class="w-full pl-10 pr-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400">
					</div>
				</div>
				<div class="flex gap-2 w-full md:w-auto">
					<select id="paymentFilter" class="w-full md:w-auto bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
						<option value="">All Payment</option>
						<option value="Pending">Unpaid</option>
						<option value="Partial">Partial</option>
						<option value="Paid">Paid</option>
					</select>
				</div>
			</div>

			<!-- Tabs -->
			<div class="flex space-x-2 w-full mb-6">
				<button id="salesTab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #FFF1DA; border-color: #FDE68A; color: #111827;">Orders</button>
				<button id="customersTab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #475569; border-color: #64748b; color: #FFFFFF;">Customers</button>
			</div>


			<!-- Sales Orders Table -->
			<div id="salesTable" class="w-full overflow-y-auto custom-scrollbar rounded-xl border border-slate-600" style="max-height: 65vh;">
				<table class="w-full table-fixed border-collapse text-left text-xs text-white">
					<thead >
						<tr class="bg-slate-700 text-slate-300 border-b border-slate-600 sticky top-0 z-10">
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Order #</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Customer</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Order Date</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Due Date</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Delivery Date</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Production Status</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-center">Total Amount</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Payment Status</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Action</th>
						</tr>
					</thead>
					<tbody id="salesTbody" class="divide-y divide-slate-600">
						@forelse($salesOrders as $order)
						@include('partials.sales-order-row', ['order' => $order])
						@empty
						<tr>
							<td colspan="9" class="text-center py-4 text-slate-400">No orders yet. Click "New Order" to create one.</td>
						</tr>
						@endforelse
						<tr id="salesNoMatch" class="hidden">
							<td colspan="9" class="text-center py-4 text-slate-400">No matching orders found</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Customers Table -->
			<div id="customerTable" class="hidden w-full overflow-y-auto custom-scrollbar rounded-xl border border-slate-600" style="max-height: 65vh;">
				<table class="w-full table-fixed border-collapse text-left text-xs text-white">
					<thead >
						<tr class="bg-slate-700 text-slate-300 border-b border-slate-600 sticky top-0 z-10">
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-xl">Name</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Type</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Contact</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Email</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Total Orders</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Total Spent</th>
							<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-center rounded-tr-xl">Action</th>
						</tr>
					</thead>
					<tbody id="customersTbody" class="divide-y divide-slate-600">
						@forelse($customers as $customer)
						<tr class="hover:bg-slate-600 transition cursor-pointer data-row">
							<td class="px-3 py-3 font-medium text-slate-300 border-l-4 border-amber-500">{{ $customer->name }}</td>
							<td class="px-3 py-3">
								@php $ctBg = $customerTypeBg[$customer->customer_type] ?? '#e5e7eb'; @endphp
								<span class="inline-block text-xs font-semibold text-white px-2 py-0.5 rounded" style="background: {{ $ctBg }};">{{ $customer->customer_type }}</span>
							</td>
							<td class="px-3 py-3 text-slate-300">{{ $customer->phone ?: 'N/A' }}</td>
							<td class="px-3 py-3 text-slate-300">{{ $customer->email }}</td>
							<td class="px-3 py-3 text-slate-300">{{ $customer->totalOrders() }}</td>
							<td class="px-3 py-3 font-bold text-slate-300">₱{{ number_format($customer->totalSpent(), 2) }}</td>
							<td class="px-3 py-3 text-center">
								@php
								$productsSummary = [];
								foreach($customer->salesOrders->where('status', '!=', 'Cancelled') as $order) {
									foreach($order->items as $item) {
										$productName = $item->product->product_name ?? 'Unknown Product';
										if (!isset($productsSummary[$productName])) {
											$productsSummary[$productName] = 0;
										}
										$productsSummary[$productName] += ($item->quantity - $item->cancelled_quantity);
									}
								}
								@endphp

								<div class="flex items-center justify-center space-x-2">
									@php
									$customerData = [
										'id' => $customer->id,
										'name' => $customer->name,
										'type' => $customer->customer_type,
										'phone' => $customer->phone,
										'email' => $customer->email,
										'totalOrders' => $customer->totalOrders(),
										'totalSpent' => $customer->totalSpent(),
										'productsSummary' => $productsSummary,
									];
									@endphp
									<button class="p-1.5 hover:bg-slate-500 rounded text-slate-400 hover:text-white transition-colors" title="View" data-customer='@json($customerData)' onclick="openViewCustomerModalFromData(this)">
										<svg class="w-4 h-4" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="currentColor" fill="none">
                                            <path d="M53.79,33.1a.51.51,0,0,0,0-.4C52.83,30.89,45.29,17.17,32,16.84S11,30.61,9.92,32.65a.48.48,0,0,0,0,.48C11.1,35.06,19.35,48.05,29.68,49,41.07,50,50.31,42,53.79,33.1Z"/>
                                            <circle cx="31.7" cy="32.76" r="6.91"/>
                                        </svg>
									</button>
									<button onclick="openEditCustomerModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->customer_type }}', '{{ $customer->phone }}', '{{ $customer->email }}')" class="p-1.5 hover:bg-slate-500 rounded text-slate-400 hover:text-amber-400 transition-colors" title="Edit">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
									</button>
									<button type="button" onclick="openDeleteCustomerModal({{ $customer->id }}, '{{ $customer->name }}')" class="p-1.5 hover:bg-slate-500 rounded text-slate-400 hover:text-red-400 transition-colors" title="Delete">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
									</button>
								</div>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="7" class="text-center py-4">No customers yet. Click "New Customer" to add one.</td>
						</tr>
						@endforelse
						<tr id="customersNoMatch" class="hidden">
							<td colspan="7" class="text-center py-4">No matches</td>
						</tr>
					</tbody>
				</table>
			</div>

		</section>


		<!-- View Customer Modal -->
		<div id="viewCustomerModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden" style="z-index: 99999;">
			<div class="flex items-center justify-center min-h-screen p-4">
				<div class="bg-amber-50 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
					<div class="p-4">
						<div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
							<h3 class="text-xl font-bold text-gray-900">Customer Details</h3>
							<button onclick="closeModal('viewCustomerModal')" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
								</svg>
							</button>
						</div>
						
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Name</span>
								<p id="viewCustomerName" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Type</span>
								<p id="viewCustomerType" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Contact #</span>
								<p id="viewCustomerPhone" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Email</span>
								<p id="viewCustomerEmail" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Total Orders</span>
								<p id="viewCustomerOrders" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
							<div class="bg-white p-3 rounded-xl border-2 border-gray-300">
								<span class="text-xs font-bold text-gray-600 uppercase">Total Spent</span>
								<p id="viewCustomerSpent" class="text-sm font-bold text-gray-900 mt-1"></p>
							</div>
						</div>

						<!-- Products Purchased Section -->
						<div class="border-t-2 border-gray-300 pt-4">
							<h4 class="text-lg font-bold text-gray-900 mb-4">Products Purchased</h4>
							<div id="viewCustomerProducts" class="bg-white rounded-xl border-2 border-gray-300 overflow-hidden">
								<!-- Products will be inserted here -->
							</div>
						</div>

						<div class="flex justify-end space-x-2 mt-6">
							<button type="button" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all" onclick="closeModal('viewCustomerModal')">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Edit Customer Modal -->
		<div id="editCustomerModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden" style="z-index: 99999;">
			<div class="flex items-center justify-center min-h-screen p-4">
				<div class="bg-amber-50 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
					<div class="p-4">
						<div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
							<h3 class="text-xl font-bold text-gray-900">Edit Customer</h3>
							<button onclick="closeModal('editCustomerModal')" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
								</svg>
							</button>
						</div>
						
						<form id="editCustomerForm" method="POST" action="" class="editCustomerForm">
							@csrf
							@method('PUT')
							<div class="grid gap-4">
								<div>
									<label class="block text-sm font-bold text-gray-900 mb-3">Name <span class="text-red-500">*</span></label>
									<input type="text" id="editCustomerName" name="name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required minlength="2" maxlength="255" pattern="[a-zA-Z\s'-]+" title="Name must contain only letters, spaces, hyphens, and apostrophes">
									<p class="text-gray-400 text-xs mt-1">This field is required. Enter customer name.</p>
								</div>
								<div class="grid grid-cols-2 gap-4">
									<div>
										<label class="block text-sm font-bold text-gray-900 mb-3">Type <span class="text-red-500">*</span></label>
										<select id="editCustomerType" name="customer_type" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
											<option value="Retail">Retail</option>
											<option value="Contractor">Contractor</option>
											<option value="Wholesale">Wholesale</option>
										</select>
										<p class="text-gray-400 text-xs mt-1">This field is required. Select a customer type.</p>
									</div>
									<div>
										<label class="block text-sm font-bold text-gray-900 mb-3">Phone <span class="text-orange-500">**</span></label>
										<input type="tel" id="editCustomerPhone" name="phone" class="editCustomerPhone w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" placeholder="09XXXXXXXXX" maxlength="12" pattern="[0-9]{0,12}" title="Phone must be up to 12 digits">
										<p class="text-gray-400 text-xs mt-1">Enter a phone number (up to 12 digits) or email below.</p>
									</div>
								</div>
								<div>
									<label class="block text-sm font-bold text-gray-900 mb-3">Email <span class="text-orange-500">**</span></label>
									<input type="email" id="editCustomerEmail" name="email" class="editCustomerEmail w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" maxlength="255" title="Please enter a valid email address">
									<p class="text-gray-400 text-xs mt-1">** At least one contact method (phone or email) is required</p>
									<p class="editCustomerContactError text-red-500 text-xs mt-1 hidden"></p>
								</div>
								<div class="flex justify-end space-x-2 mt-6">
									<button type="button" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all" onclick="closeModal('editCustomerModal')">Cancel</button>
									<button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">Update Customer</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- New Order Modal - IMPROVED VERSION -->
		<div id="newOrderModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-[99999]">
			<div class="flex items-center justify-center min-h-screen p-3">
				<div class="bg-amber-50 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
					<!-- Header -->
					<div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-2xl z-[100000]">
						<div class="flex justify-between items-center">
							<div>
								<h3 class="text-xl font-bold flex items-center gap-1.5">
									<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
									</svg>
									Create New Order
								</h3>
								<p class="text-slate-300 text-xs mt-1">Add a new sales order for a customer</p>
							</div>
							<button onclick="closeModal('newOrderModal')" class="text-white hover:text-slate-300 hover:bg-white/10 rounded-xl p-2 transition-all">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
						<form method="POST" action="{{ route('sales-orders.store') }}" id="newOrderForm" onsubmit="return confirmSalesOrder(event)">
							@csrf
							<div class="space-y-5">
								<!-- Customer Selection -->
								<div>
									<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
										</svg>
										Customer <span class="text-red-500">*</span>
									</label>
									<select name="customer_id" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('customer_id') border-red-500 @enderror" required>
										<option value="">-- Select Customer --</option>
										@foreach($customers as $c)
										<option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->customer_type }})</option>
										@endforeach
									</select>
									@error('customer_id')
									<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
										<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
										</svg>
										{{ $message }}
									</p>
									@enderror
								</div>

								<div class="grid grid-cols-1 gap-5">
									<!-- Delivery Date -->
									<div>
										<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
											<svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
											</svg>
											Delivery Date <span class="text-red-500">*</span>
										</label>
										<input type="date" name="delivery_date" id="newOrderDeliveryDate" min="{{ date('Y-m-d') }}" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('delivery_date') border-red-500 @enderror" value="{{ old('delivery_date') }}" required>
										@error('delivery_date')
										<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
											<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
												<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
											</svg>
											{{ $message }}
										</p>
										@enderror
									</div>
								</div>

								<!-- Product Selection Section -->
								<div class="pt-4 border-t-2 border-gray-300">
									<h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-1.5">
										<span class="w-1 h-6 bg-amber-500 rounded"></span>
										Order Items
									</h4>

									<div class="bg-white rounded-xl border-2 border-slate-300 p-5 shadow-lg">
										<div class="grid grid-cols-1 md:grid-cols-3 gap-3">
											<!-- Product Selection -->
											<div>
												<label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
													<svg class="w-3 h-3 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
														<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
													</svg>
													Product <span class="text-red-500">*</span>
												</label>
												<select id="newItemProduct" name="items[0][product_id]" class="w-full border-2 border-gray-300 rounded-lg px-3 py-1.5 text-base focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all @error('items.0.product_id') border-red-500 @enderror" required>
													<option value="">-- Select Product --</option>
													@foreach($products as $p)
													<option value="{{ $p->id }}" data-price="{{ number_format($p->selling_price,2,'.','') }}" data-max="{{ $p->max_producible ?? '' }}">{{ $p->product_name }}</option>
													@endforeach
												</select>
												@error('items.0.product_id')
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
												<input id="newItemQty" type="number" min="1" name="items[0][quantity]" class="w-full border-2 border-gray-300 rounded-lg px-3 py-1.5 text-base font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all @error('items.0.quantity') border-red-500 @enderror" placeholder="1" value="{{ old('items.0.quantity') }}" required>
												@error('items.0.quantity')
												<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
												@enderror
												<p id="newItemQtyError" class="text-red-500 text-xs mt-1 hidden"></p>
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
												<input id="newItemUnitPrice" type="text" class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-base font-bold bg-gray-100 text-gray-600" value="" placeholder="Auto-filled" disabled>
												<input id="newItemUnitPriceHidden" type="hidden" name="items[0][unit_price]" value="">
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
												<span id="newItemLineTotal" class="text-xl font-bold text-amber-700">₱0.00</span>
											</div>
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
										<span class="font-semibold">Required fields:</span> Customer, Delivery Date, Product
									</div>
									<div class="flex space-x-3">
										<button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-base hover:bg-gray-100 transition-all shadow-sm" onclick="closeModal('newOrderModal')">
											Cancel
										</button>
										<button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-base rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all flex items-center gap-1.5">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
											</svg>
											Create Order
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Add New Customer Modal -->
		<div id="newCustomerModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-[99999]">
			<div class="flex items-center justify-center min-h-screen p-3">
				<div class="bg-amber-50 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700">
					<!-- Header -->
					<div class="sticky top-0 bg-gradient-to-r from-slate-700 to-slate-800 p-3 text-white rounded-t-2xl z-[100000]">
						<div class="flex justify-between items-center">
							<div>
								<h3 class="text-xl font-bold flex items-center gap-1.5">
									<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
									</svg>
									Add New Customer
								</h3>
								<p class="text-slate-300 text-xs mt-1">Register a new customer to the system</p>
							</div>
							<button onclick="closeModal('newCustomerModal')" class="text-white hover:text-slate-300 hover:bg-white/10 rounded-xl p-2 transition-all">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
						<form method="POST" action="{{ route('customers.store') }}" id="newCustomerForm" onsubmit="return confirmCustomer(event)">
							@csrf
							<div class="space-y-5">
								<!-- Basic Information Section -->
								<div>
									<h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-1.5">
										<span class="w-1 h-6 bg-amber-500 rounded"></span>
										Basic Information
									</h4>

									<div class="space-y-4">
										<!-- Customer Name -->
										<div>
											<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
												<svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
												</svg>
												Customer Name <span class="text-red-500 text-base">*</span>
											</label>
											<input type="text" name="name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" required minlength="2" maxlength="255" pattern="[a-zA-Z\s'-]+" title="Name must contain only letters, spaces, hyphens, and apostrophes" placeholder="Enter full name (e.g., Juan Dela Cruz)">
											@error('name')
											<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
												<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
												</svg>
												{{ $message }}
											</p>
											@else
											<p class="text-gray-500 text-xs mt-2">Enter customer's full name (letters, spaces, hyphens, and apostrophes only)</p>
											@enderror
										</div>

										<!-- Customer Type -->
										<div>
											<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
												<svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
													<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
												</svg>
												Customer Type <span class="text-red-500 text-base">*</span>
											</label>
											<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
												@foreach(['Retail','Contractor','Wholesale'] as $index => $t)
												<label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:border-amber-500 hover:bg-amber-50 @error('customer_type') border-red-500 @else border-gray-300 @enderror">
													<input type="radio" name="customer_type" value="{{ $t }}" class="peer sr-only" {{ old('customer_type') === $t ? 'checked' : '' }} required>
													<div class="peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-orange-600 peer-checked:text-white absolute inset-0 rounded-xl opacity-0 peer-checked:opacity-100 transition-opacity"></div>
													<div class="relative z-10 flex flex-col items-center">
														@if($t === 'Retail')
														<svg class="w-8 h-8 mb-2 peer-checked:text-white text-amber-600" fill="currentColor" viewBox="0 0 20 20">
															<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
														</svg>
														@elseif($t === 'Contractor')
														<svg class="w-8 h-8 mb-2 peer-checked:text-white text-amber-600" fill="currentColor" viewBox="0 0 20 20">
															<path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
															<path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
														</svg>
														@else
														<svg class="w-8 h-8 mb-2 peer-checked:text-white text-amber-600" fill="currentColor" viewBox="0 0 20 20">
															<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
														</svg>
														@endif
														<span class="font-bold text-xs peer-checked:text-white text-gray-900">{{ $t }}</span>
													</div>
												</label>
												@endforeach
											</div>
											@error('customer_type')
											<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
												<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
												</svg>
												{{ $message }}
											</p>
											@else
											<p class="text-gray-500 text-xs mt-2">Select the customer's business type</p>
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

									<!-- Important Notice -->
									<div class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-400 rounded-xl p-3 mb-4 shadow-lg">
										<div class="flex items-start gap-3">
											<svg class="w-6 h-6 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
												<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
											</svg>
											<div>
												<p class="text-orange-900 text-xs font-bold mb-1">Contact Requirement</p>
												<p class="text-orange-800 text-xs">At least <span class="font-bold">one contact method</span> (phone number <span class="font-bold">OR</span> email address) is required</p>
											</div>
										</div>
									</div>

									<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
										<!-- Phone Number -->
										<div class="bg-white rounded-xl border-2 border-gray-300 p-3 shadow-sm transition-all focus-within:border-amber-500 focus-within:shadow-lg @error('phone') border-red-500 @enderror @error('contact') border-red-500 @enderror">
											<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
												<svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
													<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
												</svg>
												Phone Number
												<span class="text-orange-500 text-xs font-bold ml-1">**</span>
											</label>
											<input type="tel" name="phone" id="newCustomerPhone" class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" value="{{ old('phone') }}" placeholder="09XXXXXXXXX" maxlength="12" pattern="[0-9]{0,12}" title="Phone must be up to 12 digits">
											@error('phone')
											<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
												<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
												</svg>
												{{ $message }}
											</p>
											@else
											<p class="text-gray-500 text-xs mt-2">Up to 12 digits</p>
											@enderror
										</div>

										<!-- Email Address -->
										<div class="bg-white rounded-xl border-2 border-gray-300 p-3 shadow-sm transition-all focus-within:border-amber-500 focus-within:shadow-lg @error('email') border-red-500 @enderror @error('contact') border-red-500 @enderror">
											<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
												<svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
													<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
													<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
												</svg>
												Email Address
												<span class="text-orange-500 text-xs font-bold ml-1">**</span>
											</label>
											<input type="email" name="email" id="newCustomerEmail" class="w-full border-2 border-gray-200 rounded-lg px-3 py-1.5 text-base font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" value="{{ old('email') }}" maxlength="255" title="Please enter a valid email address" placeholder="customer@example.com">
											@error('email')
											<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
												<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
												</svg>
												{{ $message }}
											</p>
											@enderror
											@error('contact')
											<p class="text-red-500 text-xs mt-2 flex items-center gap-1">
												<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
												</svg>
												{{ $message }}
											</p>
											@else
											<p class="text-gray-500 text-xs mt-2">Valid email format</p>
											@enderror
										</div>
									</div>

									<!-- Contact Error Message -->
									<div id="newCustomerContactError" class="mt-3 p-3 bg-red-50 border-2 border-red-400 rounded-xl hidden">
										<p class="text-red-600 text-xs font-bold flex items-center gap-1.5">
											<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
												<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
											</svg>
											Please provide at least one contact method (phone or email)
										</p>
									</div>
								</div>

								<!-- Optional Address Section -->
								<div class="pt-4 border-t-2 border-gray-300">
									<label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
										</svg>
										Address (Optional)
									</label>
									<textarea name="address" rows="3" class="w-full border-2 border-gray-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Enter customer's full address (Street, Barangay, City, Province)">{{ old('address') }}</textarea>
									<p class="text-gray-500 text-xs mt-2">Complete address helps with deliveries and record keeping</p>
								</div>

								<!-- Action Buttons -->
								<div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
									<div class="flex items-center gap-3 text-xs">
										<div class="flex items-center gap-1">
											<span class="text-red-500 font-bold text-base">*</span>
											<span class="text-gray-600">Required</span>
										</div>
										<div class="flex items-center gap-1">
											<span class="text-orange-500 font-bold">**</span>
											<span class="text-gray-600">One required</span>
										</div>
									</div>
									<div class="flex space-x-3">
										<button type="button" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-base hover:bg-gray-100 transition-all shadow-sm" onclick="closeModal('newCustomerModal')">
											Cancel
										</button>
										<button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-base rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all flex items-center gap-1.5">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
											</svg>
											Add Customer
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<!-- Confirmation Modal for Sales Order - Add -->
<div id="confirmSalesOrderModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001] flex" onclick="if(event.target === this) closeConfirmSalesOrder()">
	<div class="flex items-center justify-center min-h-screen p-3 w-full">
		<div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700">
			<div class="sticky top-0 bg-gradient-to-r from-green-600 to-green-700 p-3 text-white rounded-t-xl z-10">
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-bold">Confirm Sales Order</h3>
					<button onclick="closeConfirmSalesOrder()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="p-6">
				<p class="text-slate-700 mb-6">Create this new sales order? Please review all details before confirming.</p>
				<div class="flex gap-3">
					<button type="button" onclick="closeConfirmSalesOrder()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Review Again</button>
					<button type="button" onclick="submitSalesOrder()" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Confirm</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Confirmation Modal for Cancelling Sales Order -->
<div id="confirmCancelSalesOrderModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001] flex" onclick="if(event.target === this) closeCancelSalesOrderModal()">
	<div class="flex items-center justify-center min-h-screen p-3 w-full">
		<div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700">
			<div class="sticky top-0 bg-gradient-to-r from-red-600 to-red-700 p-3 text-white rounded-t-xl z-10">
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-bold">Cancel Sales Order</h3>
					<button onclick="closeCancelSalesOrderModal()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="p-6">
				<p class="text-slate-700 mb-6">Cancel sales order <span id="cancelSalesOrderNumber" class="font-semibold">-</span>? This action cannot be undone.</p>
				<div class="flex gap-3">
					<button type="button" onclick="closeCancelSalesOrderModal()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Keep It</button>
					<button type="button" onclick="submitCancelSalesOrder()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Confirmation Modal for Customer -->
<div id="confirmCustomerModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001] flex" onclick="if(event.target === this) closeConfirmCustomer()">
	<div class="flex items-center justify-center min-h-screen p-3 w-full">
		<div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700">
			<div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 p-3 text-white rounded-t-xl z-10">
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-bold">Add New Customer</h3>
					<button onclick="closeConfirmCustomer()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="p-6">
				<p class="text-slate-700 mb-6">Register this new customer to the system? Their information will be saved for future orders.</p>
				<div class="flex gap-3">
					<button type="button" onclick="closeConfirmCustomer()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Cancel</button>
					<button type="button" onclick="submitCustomer()" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Add</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Delete Customer Confirmation Modal -->
<div id="deleteCustomerModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-[100001] flex" onclick="if(event.target === this) closeDeleteCustomerModal()">
	<div class="flex items-center justify-center min-h-screen p-3 w-full">
		<div class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700">
			<div class="sticky top-0 bg-gradient-to-r from-red-600 to-red-700 p-3 text-white rounded-t-xl z-10">
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-bold">Delete Customer</h3>
					<button onclick="closeDeleteCustomerModal()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="p-6">
				<p class="text-slate-700 mb-6">Delete <span id="deleteCustomerName" class="font-semibold">-</span>? This action cannot be undone.</p>
				<div class="flex gap-3">
					<button type="button" onclick="closeDeleteCustomerModal()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">No, Keep</button>
					<button type="button" onclick="submitDeleteCustomer()" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold shadow-lg hover:shadow-xl">Yes, Delete</button>
				</div>
			</div>
		</div>
</div>

<script>
	function openModal(modalId) {
		document.getElementById(modalId).classList.remove('hidden');
	}

	function closeModal(modalId) {
		document.getElementById(modalId).classList.add('hidden');
	}

	function openEditCustomerModal(customerId, name, type, phone, email) {
		document.getElementById('editCustomerName').value = name || '';
		document.getElementById('editCustomerType').value = type || 'Retail';
		document.getElementById('editCustomerPhone').value = phone || '';
		document.getElementById('editCustomerEmail').value = email || '';
		document.getElementById('editCustomerForm').action = `/customers/${customerId}`;
		openModal('editCustomerModal');
	}


	function openViewCustomerModalFromData(btn) {
		const data = btn.getAttribute('data-customer');
		if (!data) return;
		const customer = JSON.parse(data);

		// Customer type badge colors
		const typeColors = {
			'Wholesale': '#64B5F6',
			'Retail': '#6366F1',
			'Contractor': '#BA68C8'
		};

		document.getElementById('viewCustomerName').textContent = customer.name || '—';
		const typeElement = document.getElementById('viewCustomerType');
		typeElement.innerHTML = `<span class="px-2 py-0.5 rounded text-white text-xs" style="background: ${typeColors[customer.type] || '#e5e7eb'};">${customer.type}</span>`;
		document.getElementById('viewCustomerPhone').textContent = customer.phone || '—';
		document.getElementById('viewCustomerEmail').textContent = customer.email || '—';
		document.getElementById('viewCustomerOrders').textContent = customer.totalOrders || '0';
		document.getElementById('viewCustomerSpent').textContent = '₱' + (customer.totalSpent ? parseFloat(customer.totalSpent).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : '0.00');

		const productsContainer = document.getElementById('viewCustomerProducts');
		if (customer.productsSummary && Object.keys(customer.productsSummary).length > 0) {
			let tableHTML = `
				<table class="w-full text-sm border-collapse">
					<thead>
						<tr class="bg-slate-700 text-white">
							<th class="px-3 py-2 text-left font-semibold">Product Name</th>
							<th class="px-3 py-2 text-right font-semibold">Total Quantity</th>
						</tr>
					</thead>
					<tbody>
				`;
			for (const [productName, totalQty] of Object.entries(customer.productsSummary)) {
				tableHTML += `
					<tr class="border-b border-gray-200 hover:bg-gray-50 transition">
						<td class="px-3 py-2">${productName}</td>
						<td class="px-3 py-2 text-right font-medium">${totalQty} pcs</td>
					</tr>
				`;
			}
		
			tableHTML += `
					</tbody>
				</table>
			`;
			productsContainer.innerHTML = tableHTML;
		} else {
			productsContainer.innerHTML = '<p class="text-slate-500 text-sm p-4 text-center">No products purchased yet</p>';
		}

		openModal('viewCustomerModal');
	}

	(function() {
		const salesTab = document.getElementById('salesTab');
		const customersTab = document.getElementById('customersTab');
		const salesTable = document.getElementById('salesTable');
		const customerTable = document.getElementById('customerTable');
		const headerBtn = document.getElementById('headerBtn');
		const searchInput = document.getElementById('searchInput');
		const statusFilter = document.getElementById('statusFilter');
		const paymentFilter = document.getElementById('paymentFilter');
		const salesTbody = document.getElementById('salesTbody');
		const customersTbody = document.getElementById('customersTbody');
		const salesNoMatch = document.getElementById('salesNoMatch');
		const customersNoMatch = document.getElementById('customersNoMatch');
		// New order real-time pricing refs
		const newItemProduct = document.getElementById('newItemProduct');
		const newItemQty = document.getElementById('newItemQty');
		const newItemUnitPrice = document.getElementById('newItemUnitPrice');
		const newItemUnitPriceHidden = document.getElementById('newItemUnitPriceHidden');
		const newItemLineTotal = document.getElementById('newItemLineTotal');

		function setMode(mode) {
			salesTable.classList.add('hidden');
			customerTable.classList.add('hidden');

			const allTabs = [salesTab, customersTab];
			allTabs.forEach(t => {
				t.style.backgroundColor = '#475569';
				t.style.color = '#FFFFFF';
				t.style.borderColor = '#64748b';
			});

			if (mode === 'sales') {
				salesTable.classList.remove('hidden');
				headerBtn.textContent = '+ New Order';
				headerBtn.setAttribute('onclick', 'openModal("newOrderModal")');
				salesTab.style.backgroundColor = '#FFF1DA';
				salesTab.style.color = '#111827';
				salesTab.style.borderColor = 'transparent';
			} else {
				customerTable.classList.remove('hidden');
				headerBtn.textContent = '+ New Customer';
				headerBtn.setAttribute('onclick', 'openModal("newCustomerModal")');
				customersTab.style.backgroundColor = '#FFF1DA';
				customersTab.style.color = '#111827';
				customersTab.style.borderColor = 'transparent';
			}
			applyFilters();
		}

		salesTab.addEventListener('click', () => setMode('sales'));
		customersTab.addEventListener('click', () => setMode('customers'));


		function stringIncludes(haystack, needle) {
			return haystack.toLowerCase().includes(needle.toLowerCase());
		}

		function applyFilters() {
			const q = searchInput.value.trim();
			const sVal = statusFilter ? statusFilter.value : '';
			const pVal = paymentFilter.value;
			const inSales = !salesTable.classList.contains('hidden');

			if (inSales) {
				let any = false;
				const rows = salesTbody.querySelectorAll('tr.data-row');
				rows.forEach(tr => {
					let show = true;
					if (q) {
						const text = tr.textContent || '';
						show = show && stringIncludes(text, q);
					}
					if (sVal) {
						show = show && (tr.dataset.status === sVal);
					}
					if (pVal) {
						show = show && (tr.dataset.payment === pVal);
					}
					tr.classList.toggle('hidden', !show);
					any = any || show;
				});
				salesNoMatch.classList.toggle('hidden', any);
			} else {
				let any = false;
				const rows = customersTbody.querySelectorAll('tr.data-row');
				rows.forEach(tr => {
					let show = true;
					if (q) {
						const nameCell = tr.querySelector('td:nth-child(1)');
						const typeCell = tr.querySelector('td:nth-child(2)');
						const contactCell = tr.querySelector('td:nth-child(3)');
						const hay = [nameCell, typeCell, contactCell].map(c => (c?.textContent || '')).join(' ');
						show = show && stringIncludes(hay, q);
					}
					tr.classList.toggle('hidden', !show);
					any = any || show;
				});
				if (customersNoMatch) customersNoMatch.classList.toggle('hidden', any);
			}
		}


		searchInput.addEventListener('input', applyFilters);
		if (statusFilter) statusFilter.addEventListener('change', applyFilters);
		paymentFilter.addEventListener('change', applyFilters);

		// ---- Real-time unit price + line total for New Order modal ----
		function toNumber(value) {
			const n = parseFloat(value);
			return Number.isFinite(n) ? n : 0;
		}

		function formatCurrency(num) {
			return '₱' + num.toLocaleString(undefined, {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		function getSelectedUnitPrice() {
			if (!newItemProduct) return 0;
			const opt = newItemProduct.options[newItemProduct.selectedIndex];
			const priceAttr = opt ? opt.getAttribute('data-price') : null;
			return toNumber(priceAttr);
		}

		async function fetchLatestPrice(productId) {
			if (!productId) return 0;
			try {
				const response = await fetch(`/inventory/${productId}/details?type=product`);
				const data = await response.json();
				if (data && data.item && data.item.selling_price) {
					return toNumber(data.item.selling_price);
				}
			} catch (error) {
				console.error('Error fetching latest price:', error);
			}
			return getSelectedUnitPrice(); // Fallback to data-price if AJAX fails
		}

		async function updatePricingFields() {
			const unit = getSelectedUnitPrice();
			const qty = toNumber(newItemQty?.value || '0');
			
			if (newItemUnitPrice) newItemUnitPrice.value = unit ? formatCurrency(unit) : 'Fetching...';
			if (newItemUnitPriceHidden) newItemUnitPriceHidden.value = unit ? unit.toFixed(2) : '';
			
			const total = unit * (qty || 0);
			if (newItemLineTotal) newItemLineTotal.textContent = 'Line total: ' + formatCurrency(total);
		}

		if (newItemProduct) {
			newItemProduct.addEventListener('change', async () => {
				const productId = newItemProduct.value;
				if (!productId) return;

				// Show fetching state
				if (newItemUnitPrice) newItemUnitPrice.value = 'Fetching...';

				const latestPrice = await fetchLatestPrice(productId);
				
				// Update the data-price attribute so subsequent calls to getSelectedUnitPrice use the fresh value
				const opt = newItemProduct.options[newItemProduct.selectedIndex];
				if (opt) {
					opt.setAttribute('data-price', latestPrice.toFixed(2));
				}

				// If quantity empty or <1, default to 1 on first select
				if (newItemQty && (!newItemQty.value || toNumber(newItemQty.value) < 1)) {
					newItemQty.value = '1';
				}
				updatePricingFields();
			});
		}
		if (newItemQty) {
			['input', 'change', 'blur'].forEach(evt => newItemQty.addEventListener(evt, () => {
				if (newItemQty.value && toNumber(newItemQty.value) < 1) newItemQty.value = '1';
				updatePricingFields();
			}));
		}

		// Initialize pricing display if fields already have values
		updatePricingFields();

		// ---- Contact method validation for customer forms ----
		function validateContactMethod(phoneInput, emailInput, errorMessageEl) {
			const phone = (phoneInput?.value || '').trim();
			const email = (emailInput?.value || '').trim();
			const hasError = !phone && !email;

			if (hasError) {
				phoneInput?.classList.add('border-red-500');
				emailInput?.classList.add('border-red-500');
				if (errorMessageEl) {
					errorMessageEl.textContent = 'Please provide at least a phone number or email address.';
					errorMessageEl.classList.remove('hidden');
				}
			} else {
				phoneInput?.classList.remove('border-red-500');
				emailInput?.classList.remove('border-red-500');
				if (errorMessageEl) {
					errorMessageEl.classList.add('hidden');
					errorMessageEl.textContent = '';
				}
			}

			return !hasError;
		}

		// New Customer form validation
		const newCustomerForm = document.getElementById('newCustomerForm');
		if (newCustomerForm) {
			const newPhoneInput = document.getElementById('newCustomerPhone');
			const newEmailInput = document.getElementById('newCustomerEmail');
			const newContactError = document.getElementById('newCustomerContactError');

			newCustomerForm.addEventListener('submit', function(e) {
				if (!validateContactMethod(newPhoneInput, newEmailInput, newContactError)) {
					e.preventDefault();
					return false;
				}
			});

			// Real-time validation as user types
			newPhoneInput?.addEventListener('blur', () => validateContactMethod(newPhoneInput, newEmailInput, newContactError));
			newEmailInput?.addEventListener('blur', () => validateContactMethod(newPhoneInput, newEmailInput, newContactError));
		}

		// Edit Customer form validation (for each customer)
		const editCustomerForms = document.querySelectorAll('.editCustomerForm');
		editCustomerForms.forEach(form => {
			const phoneInput = form.querySelector('.editCustomerPhone');
			const emailInput = form.querySelector('.editCustomerEmail');
			const contactError = form.querySelector('.editCustomerContactError');

			form.addEventListener('submit', function(e) {
				if (!validateContactMethod(phoneInput, emailInput, contactError)) {
					e.preventDefault();
					return false;
				}
			});

			// Real-time validation as user types
			phoneInput?.addEventListener('blur', () => validateContactMethod(phoneInput, emailInput, contactError));
			emailInput?.addEventListener('blur', () => validateContactMethod(phoneInput, emailInput, contactError));
		});

		// default: sales tab
		setMode('sales');
	})();

	// Auto-open modal if validation errors exist for new customer
	@if ($errors->any() && (old('name') || old('customer_type') || old('phone') || old('email')))
		// Check if this is from the new customer form (not edit customer)
		// Edit customer forms have customer_id in old data
		@if (!old('customer_id') && !request()->has('_method'))
			document.addEventListener('DOMContentLoaded', function() {
				// Open the new customer modal
				openModal('newCustomerModal');
				// Switch to customers tab to show the modal properly
				const customersTab = document.getElementById('customersTab');
				if (customersTab) {
					customersTab.click();
				}
				// Scroll to the top of the modal to show errors
				setTimeout(function() {
					const modal = document.getElementById('newCustomerModal');
					if (modal) {
						const modalContent = modal.querySelector('.modal-content');
						if (modalContent) {
							modalContent.scrollTop = 0;
						}
					}
				}, 100);
			});
		@endif
	@endif

	// Auto-sync Due Date with Delivery Date
	document.addEventListener('DOMContentLoaded', function() {
		// For New Order Modal
		const newDeliveryDate = document.getElementById('newOrderDeliveryDate');
		const newDueDate = document.getElementById('newOrderDueDate');
		if (newDeliveryDate && newDueDate) {
			newDeliveryDate.addEventListener('change', function() {
				if (!newDueDate.value || newDueDate.dataset.autoSynced === 'true' || newDueDate.value === this.dataset.lastAutoValue) {
					newDueDate.value = this.value;
					newDueDate.dataset.autoSynced = 'true';
					this.dataset.lastAutoValue = this.value;
				}
			});
			newDueDate.addEventListener('input', function() {
				this.dataset.autoSynced = 'false';
			});
		}

		// For Edit Order Modals (Delegated)
		document.addEventListener('change', function(e) {
			if (e.target.classList.contains('editDeliveryDate')) {
				const container = e.target.closest('form') || e.target.closest('div.grid');
				const dueInput = container.querySelector('.editDueDate');
				if (dueInput) {
					if (!dueInput.value || dueInput.dataset.autoSynced === 'true' || dueInput.value === e.target.dataset.lastAutoValue) {
						dueInput.value = e.target.value;
						dueInput.dataset.autoSynced = 'true';
						e.target.dataset.lastAutoValue = e.target.value;
					}
				}
			}
		});
		document.addEventListener('input', function(e) {
			if (e.target.classList.contains('editDueDate')) {
				e.target.dataset.autoSynced = 'false';
			}
		});
	});

	// Show success notification if exists
	@if (session('success'))
		document.addEventListener('DOMContentLoaded', function() {
			showSuccessNotification('{{ session('success') }}');
		});
	@endif

	// Export Dropdown Toggle 
	const exportButton = document.getElementById('exportButton');
	const exportDropdown = document.getElementById('exportDropdown');

	if (exportButton && exportDropdown) {
		exportButton.addEventListener('click', function(e) {
			e.preventDefault(); // Prevent default action
			e.stopPropagation();

			// Toggle dropdown visibility
			exportDropdown.classList.toggle('hidden');
		});

		// Close dropdown when clicking outside
		document.addEventListener('click', function(e) {
			if (!exportButton.contains(e.target) && !exportDropdown.contains(e.target)) {
				exportDropdown.classList.add('hidden');
			}
		});

		// Prevent dropdown from closing when clicking inside it
		exportDropdown.addEventListener('click', function(e) {
			e.stopPropagation();
		});
	}

	// Add row selection functionality
	const salesTbody = document.getElementById('salesTbody');
	if (salesTbody) {
		salesTbody.addEventListener('click', function(e) {
			const row = e.target.closest('tr.data-row');

			// Ignore clicks on action buttons
			if (e.target.closest('button') || e.target.closest('form')) {
				return;
			}

			if (row) {
				// Remove selection from all rows
				const allRows = salesTbody.querySelectorAll('tr.data-row');
				allRows.forEach(r => {
					r.classList.remove('!bg-gray-600', 'border-l-4', 'border-amber-500');
				});

				// Add selection to clicked row using Tailwind classes
				row.classList.add('!bg-gray-600', 'border-l-4', 'border-amber-500');
			}
		});
	}

	// Export Functions
	function exportReceipt(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');

		// Check if a row is selected
		const selectedRow = document.querySelector('tr.data-row.border-amber-500');

		if (selectedRow) {
			// Get order number from the selected row
			const orderNumber = selectedRow.querySelector('td:first-child').textContent.trim();
			window.open(`/sales/receipt/${orderNumber}`, '_blank');
			return;
		}

		// If no row is selected, prompt user
		showErrorNotification('Please select an order by clicking on a row in the table to export as receipt.');
	}

	function exportSalesReport(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');
		// Implement sales report export (CSV/PDF)
		window.location.href = '/sales/export/report';
	}

	function exportCustomerList(event) {
		event.preventDefault();
		exportDropdown.classList.add('hidden');
		// Implement customer list export (CSV/Excel)
		window.location.href = '/customers/export';
	}

	// Confirmation Modal Functions for Sales Order
	function confirmSalesOrder(event) {
		event.preventDefault();
		document.getElementById('confirmSalesOrderModal').classList.remove('hidden');
		document.getElementById('confirmSalesOrderModal').classList.add('flex');
		return false;
	}

	function closeConfirmSalesOrder() {
		document.getElementById('confirmSalesOrderModal').classList.add('hidden');
		document.getElementById('confirmSalesOrderModal').classList.remove('flex');
	}

	function submitSalesOrder() {
		closeConfirmSalesOrder();
		// Show success notification before submitting form
		showSuccessNotification('Sales order created successfully!');
		// Submit the form normally (let Laravel handle it)
		const form = document.getElementById('newOrderForm');
		form.onsubmit = null; // Remove the onsubmit handler
		setTimeout(() => {
			form.submit();
		}, 2000);
	}

	// Confirmation Modal Functions for Cancelling Sales Order
	let currentCancelOrderId = null;

	function openCancelSalesOrderModal(id, orderNumber) {
		currentCancelOrderId = id;
		document.getElementById('cancelSalesOrderNumber').textContent = orderNumber;
		const modal = document.getElementById('confirmCancelSalesOrderModal');
		modal.classList.remove('hidden');
		modal.classList.add('flex');
	}

	function closeCancelSalesOrderModal() {
		const modal = document.getElementById('confirmCancelSalesOrderModal');
		modal.classList.add('hidden');
		modal.classList.remove('flex');
	}

	function submitCancelSalesOrder() {
		const orderNumber = document.getElementById('cancelSalesOrderNumber').textContent;
		
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/sales-orders/${currentCancelOrderId}`;

		const csrf = document.createElement('input');
		csrf.type = 'hidden';
		csrf.name = '_token';
		csrf.value = document.querySelector('meta[name="csrf-token"]').content;

		const method = document.createElement('input');
		method.type = 'hidden';
		method.name = '_method';
		method.value = 'DELETE';

		form.appendChild(csrf);
		form.appendChild(method);
		document.body.appendChild(form);
		
		closeCancelSalesOrderModal();
		showSuccessNotification(`Sales order ${orderNumber} has been cancelled successfully.`);
		setTimeout(() => {
			form.submit();
		}, 2000);
	}

	// Confirmation Modal Functions for Customer
	function confirmCustomer(event) {
		event.preventDefault();
		document.getElementById('confirmCustomerModal').classList.remove('hidden');
		document.getElementById('confirmCustomerModal').classList.add('flex');
		return false;
	}

	function closeConfirmCustomer() {
		document.getElementById('confirmCustomerModal').classList.add('hidden');
		document.getElementById('confirmCustomerModal').classList.remove('flex');
	}

	function submitCustomer() {
		closeConfirmCustomer();
		// Submit the form without showing success message
		// Let server validate and show success after successful validation
		const form = document.getElementById('newCustomerForm');
		// Remove the onsubmit to avoid infinite loop
		form.onsubmit = null;
		form.submit();
	}

	// Delete Customer Modal Functions
	let currentDeleteCustomerId = null;

	function openDeleteCustomerModal(id, customerName) {
		currentDeleteCustomerId = id;
		document.getElementById('deleteCustomerName').textContent = customerName;
		const modal = document.getElementById('deleteCustomerModal');
		modal.classList.remove('hidden');
		modal.classList.add('flex');
	}

	function closeDeleteCustomerModal() {
		const modal = document.getElementById('deleteCustomerModal');
		modal.classList.add('hidden');
		modal.classList.remove('flex');
	}

	function submitDeleteCustomer() {
		const customerName = document.getElementById('deleteCustomerName').textContent;
		
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/customers/${currentDeleteCustomerId}`;

		const csrf = document.createElement('input');
		csrf.type = 'hidden';
		csrf.name = '_token';
		csrf.value = document.querySelector('meta[name="csrf-token"]').content;

		const method = document.createElement('input');
		method.type = 'hidden';
		method.name = '_method';
		method.value = 'DELETE';

		form.appendChild(csrf);
		form.appendChild(method);
		document.body.appendChild(form);
		
		closeDeleteCustomerModal();
		showSuccessNotification(`Customer ${customerName} has been deleted successfully.`);
		setTimeout(() => {
			form.submit();
		}, 2000);
	}



// Ensure date inputs do not allow past dates (handles browser & timezone differences)
document.addEventListener('DOMContentLoaded', function() {
	try {
		const today = new Date();
		const yyyy = today.getFullYear();
		const mm = String(today.getMonth() + 1).padStart(2, '0');
		const dd = String(today.getDate()).padStart(2, '0');
		const iso = `${yyyy}-${mm}-${dd}`;
		document.querySelectorAll('input[type="date"][name="delivery_date"]').forEach(function(el) {
			// set or override min to today's date
			el.setAttribute('min', iso);
		});
	} catch (e) {
		console.warn('Could not set min for delivery_date inputs', e);
	}
});
</script>

@push('modals')
<div id="archiveModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden" style="z-index: 99999;">
	<div class="flex items-center justify-center min-h-screen p-4">
		<div class="rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl border border-slate-600" style="background-color: #FFF1DA;">
			<!-- Modal Header -->
			<div class="flex justify-between items-center p-5 border-b-2" style="border-color: #374151;">
				<div class="flex items-center gap-3">
					<div class="bg-amber-500/10 p-2 rounded-lg">
						<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
						</svg>
					</div>
					<div>
						<h3 class="text-lg font-bold" style="color: #374151;">Orders Archive</h3>
						<p class="text-gray-600 text-xs mt-0.5">History of cancelled and delivered sales orders</p>
					</div>
				</div>
				<button onclick="closeModal('archiveModal')" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-xl p-2 transition-all">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
					</svg>
				</button>
			</div>

			<div class="px-5 pt-4 pb-2">
				<input type="search" id="archiveSearchInput" placeholder="Search archived orders..." class="bg-white w-full rounded-lg px-4 py-2 text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50 border border-gray-300 mb-3 transition-all">
				
				<!-- Date Range Filters -->
				<div class="grid grid-cols-2 gap-3 mb-3">
					<div>
						<label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">From</label>
						<input type="date" id="archiveStartDate" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
					</div>
					<div>
						<label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">To</label>
						<input type="date" id="archiveEndDate" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs">
					</div>
				</div>

				<!-- Quick Filters -->
				<div class="flex flex-wrap gap-2 mb-3">
					<button onclick="applyArchiveQuickFilter('yesterday')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Yesterday</button>
					<button onclick="applyArchiveQuickFilter('last-week')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">Last Week</button>
					<button onclick="applyArchiveQuickFilter('1-month')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Month</button>
					<button onclick="applyArchiveQuickFilter('1-year')" class="px-3 py-1 bg-white border border-gray-300 rounded-full text-[10px] font-bold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-all shadow-sm">1 Year</button>
				</div>
				
				<!-- Archive Filter Buttons -->
				<div class="flex justify-between items-center mb-4">
					<div class="flex items-center gap-2 bg-white/50 p-1.5 rounded-xl border border-gray-200">
						<button onclick="filterArchive('all')" id="archiveFilterAll" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-amber-500 text-white shadow-lg archive-filter-btn">All Archived</button>
						<button onclick="filterArchive('Cancelled')" id="archiveFilterCancelled" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-500 hover:bg-gray-200 archive-filter-btn">Cancelled</button>
						<button onclick="filterArchive('Delivered')" id="archiveFilterDelivered" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-500 hover:bg-gray-200 archive-filter-btn">Delivered</button>
					</div>
					<button onclick="clearArchiveFilters()" class="inline-flex items-center gap-1 px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition-all text-xs font-medium">
						<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
						Clear Filters
					</button>
				</div>
			</div>

			<!-- Archive Table -->
			<div class="px-5 pb-5 overflow-y-auto custom-scrollbar" style="max-height: 65vh;">
				<table class="w-full border-separate border-spacing-y-2 text-left text-xs">
					<thead class="text-gray-700 bg-gray-100/80 sticky top-0 z-10">
						<tr>
							<th class="px-4 py-3 font-bold rounded-tl-xl text-center">Order #</th>
							<th class="px-4 py-3 font-bold">Customer</th>
							<th class="px-4 py-3 font-bold text-center">Started</th>
							<th class="px-4 py-3 font-bold text-center">Completed</th>
							<th class="px-4 py-3 font-bold text-center border-l border-gray-200">Status</th>
							<th class="px-4 py-3 font-bold text-right">Total Amount</th>
							<th class="px-4 py-3 font-bold text-center border-x border-gray-200">Payment</th>
							<th class="px-4 py-3 font-bold rounded-tr-xl text-center">Action</th>
						</tr>
					</thead>
					<tbody id="archiveTbody" class="bg-transparent">
						@forelse($archiveOrders as $order)
						@include('partials.sales-order-row', ['order' => $order, 'isArchive' => true])
						@empty
						<tr>
							<td colspan="8" class="text-center py-10 bg-white/40 rounded-xl shadow-sm">
								<svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
								</svg>
								<p class="text-gray-500 font-medium">No archived orders found</p>
							</td>
						</tr>
						@endforelse
						<tr id="archiveNoMatch" class="hidden">
							<td colspan="9" class="text-center py-8 text-slate-400">No matching archived orders found</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Footer -->
			<div class="px-5 py-3 border-t-2 flex justify-between items-center" style="border-color: #374151;">
				<span class="text-gray-500 text-xs font-medium">{{ $archiveOrders->count() }} archived {{ Str::plural('order', $archiveOrders->count()) }}</span>
				<button onclick="closeModal('archiveModal')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all text-sm font-bold shadow-md">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Delivery Confirmation Modal -->
<div id="deliverOrderModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center p-4 z-[99999]" style="cursor: default;">
	<div class="rounded-2xl shadow-2xl max-w-sm w-full p-6 border-2 border-slate-700 animate-fadeIn" style="background-color: #FFF1DA;" onclick="event.stopPropagation()">
		<div class="text-center mb-5">
			<div class="w-12 h-12 bg-green-500/10 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
				</svg>
			</div>
			<h3 class="font-bold text-sm mb-1" style="color: #374151;">Confirm Delivery?</h3>
			<p class="text-xs" style="color: #666;">
				Mark order <span id="deliverOrderNumber" class="font-semibold text-green-600">-</span> as delivered?
			</p>
		</div>
		<form id="deliverOrderForm" method="POST">
			@csrf
			<div class="flex justify-center gap-2">
				<button type="button" onclick="closeModal('deliverOrderModal')" class="px-4 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition-all text-xs font-medium text-gray-700">
					No, Keep Working
				</button>
				<button type="submit" class="px-4 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-xs font-medium shadow-md hover:shadow-lg">
					Yes, Deliver
				</button>
			</div>
		</form>
	</div>
</div>

<script>
let currentArchiveStatus = 'all';

function filterArchive(status) {
	currentArchiveStatus = status;
	
	// Update button styles
	document.querySelectorAll('.archive-filter-btn').forEach(btn => {
		btn.classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
		btn.classList.add('text-gray-500', 'hover:bg-gray-200');
	});
	
	const btnId = 'archiveFilter' + (status === 'all' ? 'All' : status);
	const activeBtn = document.getElementById(btnId);
	if (activeBtn) {
		activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-200');
		activeBtn.classList.add('bg-amber-500', 'text-white', 'shadow-lg');
	}

	applyArchiveFilters();
}

function applyArchiveFilters() {
	const q = document.getElementById('archiveSearchInput')?.value.trim().toLowerCase() || '';
	const start = document.getElementById('archiveStartDate')?.value || '';
	const end = document.getElementById('archiveEndDate')?.value || '';
	const tbody = document.getElementById('archiveTbody');
	const rows = tbody.querySelectorAll('tr.data-row');
	const noMatch = document.getElementById('archiveNoMatch');
	let any = false;

	rows.forEach(tr => {
		const text = (tr.textContent || '').toLowerCase();
		const status = tr.getAttribute('data-status');
		const completedDate = tr.getAttribute('data-completed-date');
		
		const matchesSearch = !q || text.includes(q);
		const matchesStatus = currentArchiveStatus === 'all' || status === currentArchiveStatus;
		
		let matchesDate = true;
		if (start || end) {
			if (completedDate) {
				if (start && completedDate < start) matchesDate = false;
				if (end && completedDate > end) matchesDate = false;
			} else {
				matchesDate = false;
			}
		}
		
		const show = matchesSearch && matchesStatus && matchesDate;
		tr.classList.toggle('hidden', !show);
		any = any || show;
	});

	if (noMatch) noMatch.classList.toggle('hidden', any);
}

function applyArchiveQuickFilter(type) {
	const startInput = document.getElementById('archiveStartDate');
	const endInput = document.getElementById('archiveEndDate');
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
	applyArchiveFilters();
}

function clearArchiveFilters() {
	const searchInput = document.getElementById('archiveSearchInput');
	const startInput = document.getElementById('archiveStartDate');
	const endInput = document.getElementById('archiveEndDate');
	
	if (searchInput) searchInput.value = '';
	if (startInput) startInput.value = '';
	if (endInput) endInput.value = '';
	
	filterArchive('all');
}

function openDeliverOrderModal(id, number) {
	document.getElementById('deliverOrderNumber').textContent = number;
	document.getElementById('deliverOrderForm').action = `/sales-orders/${id}/deliver`;
	const modal = document.getElementById('deliverOrderModal');
	modal.classList.remove('hidden');
	modal.classList.add('flex');
}

// Archive modal events
document.addEventListener('DOMContentLoaded', function() {
	const archiveSearch = document.getElementById('archiveSearchInput');
	if (archiveSearch) {
		archiveSearch.addEventListener('input', applyArchiveFilters);
	}
	const archiveStart = document.getElementById('archiveStartDate');
	const archiveEnd = document.getElementById('archiveEndDate');
	if (archiveStart) archiveStart.addEventListener('change', applyArchiveFilters);
	if (archiveEnd) archiveEnd.addEventListener('change', applyArchiveFilters);
});
</script>

<script src="{{ asset('js/sales-ajax.js') }}"></script>


    @foreach($salesOrders as $order)
        @include('partials.sales-order-modals', ['order' => $order])
    @endforeach
    @foreach($archiveOrders as $order)
        @include('partials.sales-order-modals', ['order' => $order])
    @endforeach
    @endpush
</div>
@endsection