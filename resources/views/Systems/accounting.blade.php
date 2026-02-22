@extends('layouts.system')

@section('main-content')
<style>
	/* Custom Scrollbar */
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
	@php
		$typeBg = [
			'Payment Made' => '#FFB74D',
			'Payment Received' => '#64B5F6',
			'Expense' => '#EF5350',
			'Income' => '#81C784',
		];
		$monthlyPerformance = $monthlyPerformance ?? [];
	@endphp
	<!-- Main Content -->
	<div class="flex-1 flex flex-col overflow-hidden">
	<!-- Header -->
	<div class="bg-amber-50 p-5 relative z-10 border-b border-amber-200">
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-xl font-bold text-gray-800">Accounting & Finance</h1>
				<p class="text-base text-gray-600 mt-2">Track finances, manage budgets, and generate financial reports</p>
			</div>
			<div class="flex space-x-3 relative z-50">
				<div class="relative">
					<button id="exportButtonAccounting" class="flex items-center gap-2 bg-slate-600 hover:bg-slate-500 px-4 py-2 rounded-lg text-sm text-white transition">
						<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
						</svg>
						<span>Export</span>
						<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
						</svg>
					</button>
					<!-- Export Dropdown -->
					<div id="exportDropdownAccounting" class="hidden absolute top-full right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
						<div class="py-1">
							<a href="#" onclick="exportTransactionReceipt(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
									</svg>
									<span>Transaction Receipt</span>
								</div>
							</a>
							<a href="#" onclick="exportFinancialReport(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
									</svg>
									<span>Financial Report</span>
								</div>
							</a>
							<a href="#" onclick="exportTransactionHistory(event)" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition">
								<div class="flex items-center gap-1.5">
									<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
									</svg>
									<span>Transaction History</span>
								</div>
							</a>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	<!-- Dashboard Content -->
	<div class="flex-1 p-5 bg-amber-50 overflow-y-auto">
		<!-- Summary Cards Row -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
			<!-- Total Revenue Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Total Revenue</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-amber-300 to-amber-100 bg-clip-text text-transparent">₱{{ number_format($totalRevenue, 2) }}</p>
						<p class="text-slate-300 text-xs mt-1">
							<span class="{{ $lastMonthRevenuePercentage >= 1 ? 'text-green-400' : 'text-slate-300' }}">{{ $lastMonthRevenuePercentage > 0 ? '+' : '' }}{{ $lastMonthRevenuePercentage }}% from last month</span>
						</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.dollar', ['class' => 'w-5 h-5 text-amber-400'])
					</div>
				</div>
			</div>

			<!-- Total Expenses Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Total Expenses</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-red-300 to-red-100 bg-clip-text text-transparent">₱{{ number_format($totalExpenses, 2) }}</p>
						<p class="text-slate-300 text-xs mt-1">
							<span class="{{ $lastMonthExpensesPercentage >= 1 ? 'text-red-400' : 'text-slate-300' }}">{{ $lastMonthExpensesPercentage > 0 ? '+' : '' }}{{ $lastMonthExpensesPercentage }}% from last month</span>
						</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.dollar', ['class' => 'w-5 h-5 text-red-400'])
					</div>
				</div>
			</div>

			<!-- Net Profit Card -->
			<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
				<div class="flex justify-between items-start">
					<div>
						<h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Net Profit</h3>
						<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-green-300 to-green-100 bg-clip-text text-transparent">₱{{ number_format($netProfit, 2) }}</p>
						<p class="text-slate-300 text-xs mt-1">
							<span class="{{ $lastMonthNetProfitPercentage >= 1 ? 'text-green-400' : 'text-slate-300' }}">{{ $lastMonthNetProfitPercentage > 0 ? '+' : '' }}{{ $lastMonthNetProfitPercentage }}% from last month</span>
						</p>
					</div>
					<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
						@include('components.icons.dollar', ['class' => 'w-5 h-5 text-green-400'])
					</div>
				</div>
			</div>
		</div>
			<!-- Main Content Grid -->
			<div class="grid grid-cols-1 gap-6">
				<!-- Financial Transactions Section (Full Width) -->
				<div class="w-full">
					<div class="bg-gradient-to-br from-slate-700 to-slate-800 text-white p-3 rounded-xl overflow-visible shadow-xl border border-slate-600">
						<div class="flex justify-between items-center mb-6">
							<div>
								<h2 class="text-xl font-bold text-white">Financial Transactions</h2>
								<p class="text-slate-300 text-xs font-medium mt-2">Track all income, expenses, and financial activities</p>
							</div>
							<div class="flex items-center gap-2">
								@php
									$pendingTransactionsCount = $salesOrders->where('remaining_balance', '>', 0)->count() + 
															   $purchaseOrders->where('remaining_balance', '>', 0)->count();
								@endphp
								<button onclick="openAddTransaction()" class="relative px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-1.5 font-medium">
									<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
										<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
									</svg>
									<span>Add Transaction</span>
									@if($pendingTransactionsCount > 0)
										<span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white shadow-sm flex items-center justify-center min-w-[20px] h-5">
											{{ $pendingTransactionsCount }}
										</span>
									@endif
								</button>
							</div>
						</div>

						<!-- Search and Filters -->
						<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
							<div class="flex-1 max-w-md w-full">
								<div class="relative">
									<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
										<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
										</svg>
									</div>
									<input type="text" id="searchInput" placeholder="Search transactions..." class="w-full pl-10 pr-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400">
								</div>
							</div>
							<div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
								<select id="statusFilter" class="bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 w-full sm:w-auto">
									<option value="all">All Status</option>
									<option value="paid">Paid</option>
									<option value="partial">Partial</option>
									<option value="unpaid">Unpaid</option>
								</select>
								<select id="categoryFilter" class="bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 w-full sm:w-auto">
									<option value="all">All Categories</option>
									<option value="Income">Income</option>
									<option value="Expense">Expense</option>
								</select>
							</div>
						</div>

						<!-- Tabs -->
						<div class="flex space-x-2 w-full mb-6">
							<button onclick="showAccountingTab('transaction')" id="transactionTab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #FFF1DA; border-color: #FDE68A; color: #111827;">Transactions</button>
							<button onclick="showAccountingTab('reports')" id="reportsTab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #475569; border-color: #64748b; color: #FFFFFF;">Reports</button>
						</div>

						<div id="transactionContent">

							<!-- Transactions Table -->
						<div class="overflow-x-auto rounded-xl border border-slate-600">
							<table class="w-full text-left border-collapse">
								<thead>
									<tr class="table w-full table-fixed bg-slate-700/50 text-slate-300 border-b border-slate-600">
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/6">Transaction #</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/6">Date</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/12">Type</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/6">Category</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/6">Description</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/6">Amount</th>
										<th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider w-1/12">Status</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-slate-600 block overflow-y-auto custom-scrollbar" style="max-height: 50vh;" id="transactionTableBody">
									@forelse($transactions as $transaction)
										<tr class="table w-full table-fixed border-b border-slate-600 hover:bg-slate-700/50 transition-colors duration-200 transaction-row cursor-pointer" onclick="selectRow(this)"
											data-type="{{ $transaction->transaction_type }}"
											data-status="@if($transaction->purchaseOrder){{ strtolower($transaction->purchaseOrder->payment_status ?? 'paid') }}@elseif($transaction->salesOrder){{ strtolower($transaction->salesOrder->payment_status ?? 'paid') }}@else{{ 'paid' }}@endif"
											data-id="TO-{{ \Carbon\Carbon::parse($transaction->date)->format('Y') }}-{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}"
											data-date="{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}"
											data-category="@if($transaction->salesOrder){{ strtolower($transaction->salesOrder->customer->name ?? '') }}@elseif($transaction->purchaseOrder){{ strtolower($transaction->purchaseOrder->supplier->name ?? '') }}@endif"
											data-description="@if($transaction->salesOrder){{ strtolower($transaction->salesOrder->order_number ?? '') }}@elseif($transaction->purchaseOrder){{ strtolower($transaction->purchaseOrder->order_number ?? '') }}@else{{ strtolower($transaction->description ?? '') }}@endif">
											<td class="px-4 py-3 font-mono text-slate-300 w-1/6">TO-{{ \Carbon\Carbon::parse($transaction->date)->format('Y') }}-{{ str_pad($transaction->id, 3, '0', STR_PAD_LEFT) }}</td>
											<td class="px-4 py-3 font-medium text-slate-200 w-1/6">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
											<td class="px-4 py-3 w-1/12">
												<span class="text-xs font-bold {{ $transaction->transaction_type === 'Income' ? 'text-green-400' : 'text-red-400' }}">
													{{ $transaction->transaction_type === 'Income' ? 'Income' : 'Expense' }}
												</span>
											</td>
											<td class="px-4 py-3 font-medium w-1/6">
												@if($transaction->salesOrder)
													<span class="text-blue-300">{{ $transaction->salesOrder->customer->name ?? 'N/A' }}</span>
												@elseif($transaction->purchaseOrder)
													<span class="text-purple-300">{{ $transaction->purchaseOrder->supplier->name ?? 'N/A' }}</span>
												@else
													<span class="text-slate-400">N/A</span>
												@endif
											</td>
											<td class="px-4 py-3 w-1/6">
												@if($transaction->salesOrder)
													<span class="text-slate-300">{{ $transaction->salesOrder->order_number }}</span>
												@elseif($transaction->purchaseOrder)
													<span class="text-slate-300">{{ $transaction->purchaseOrder->order_number }}</span>
												@else
													<span class="text-slate-400 italic">{{ $transaction->description ?? '-' }}</span>
												@endif
											</td>
											<td class="px-4 py-3 font-bold text-left w-1/6">
												<span class="text-{{ $transaction->transaction_type === 'Income' ? 'green' : 'orange' }}-300">₱{{ number_format($transaction->amount, 2) }}</span>
											</td>
											<td class="px-4 py-3 w-1/12">
												@php
													$status = 'Paid'; // Default
													if ($transaction->salesOrder) {
														$status = $transaction->salesOrder->payment_status;
													} elseif ($transaction->purchaseOrder) {
														$status = $transaction->purchaseOrder->payment_status;
													}
													$statusColor = 'text-green-400';
													if ($status === 'Partial') $statusColor = 'text-amber-400';
													if ($status === 'Pending' || $status === 'Unpaid') $statusColor = 'text-slate-400';
												@endphp
												<span class="text-xs font-bold {{ $statusColor }}">{{ $status }}</span>
											</td>
										</tr>
									@empty
										<tr class="table w-full table-fixed">
											<td colspan="6" class="px-4 py-12 text-center text-slate-400">
												<div class="flex flex-col items-center space-y-2">
													<svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
													</svg>
													<p>No transactions found</p>
												</div>
											</td>
										</tr>
									@endforelse
								</tbody>
								</table>
							</div>
						</div>

						<div id="reportsContent" class="hidden">
							<div class="bg-slate-800/60 rounded-xl p-6 border border-slate-600">
								<div class="mb-5">
									<h3 class="text-lg font-bold text-white">Monthly Performance</h3>
									<p class="text-slate-300 text-sm">Revenue, expenses, and net profit summary</p>
								</div>
								<div class="space-y-4">
									@foreach($monthlyPerformance as $month)
										<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-700 pb-4 last:border-b-0 last:pb-0">
											<div class="flex items-center gap-6">
												<span class="text-slate-200 font-semibold w-10">{{ $month['month'] }}</span>
												<div class="text-sm">
													<p class="text-green-400 font-semibold">Revenue: ₱{{ number_format($month['revenue'], 2) }}</p>
													<p class="text-red-400 font-semibold">Expenses: ₱{{ number_format($month['expenses'], 2) }}</p>
												</div>
											</div>
											<div class="text-right">
												<p class="text-blue-400 font-bold text-lg">₱{{ number_format($month['net_profit'], 2) }}</p>
												<p class="text-slate-300 text-xs">Net Profit</p>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>

	<!-- Add Transaction Modal -->
	<div id="addTransactionModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAddTransaction()">
		<div class="bg-amber-50 rounded-xl p-6 shadow-2xl border-2 border-amber-200 w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar" onclick="event.stopPropagation()">
				<div class="flex justify-between items-center mb-6">
					<div>
						<h2 class="text-2xl font-bold text-gray-800">Add New Transaction</h2>
						<p class="text-gray-600 text-sm mt-1">Select an order to record a payment transaction</p>
					</div>
					<button onclick="closeAddTransaction()" class="text-gray-500 hover:text-gray-800 hover:bg-gray-200 p-2 rounded-lg transition">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
						</svg>
					</button>
				</div>

				<!-- Tabs -->
				<div class="flex gap-2 mb-6">
					<button onclick="showSalesOrders()" id="salesOrdersTab" class="flex-1 px-4 py-2.5 rounded-lg font-semibold text-sm transition-all bg-amber-500 text-white shadow-lg">
						Sales Orders (Income)
					</button>
					<button onclick="showPurchaseOrders()" id="purchaseOrdersTab" class="flex-1 px-4 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-300 text-gray-700 hover:bg-gray-400">
						Purchase Orders (Expense)
					</button>
				</div>

				<!-- Search and Filter -->
				<div class="flex gap-4 mb-4">
					<div class="relative flex-1">
						<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
							</svg>
						</div>
						<input type="text" id="modalSearchInput" placeholder="Search by Order # or Name..." class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-gray-800">
					</div>
					<div class="w-48">
						<select id="modalSortFilter" class="w-full h-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-gray-800">
							<option value="random">Random Order</option>
							<option value="high-low">Amount: High to Low</option>
							<option value="low-high">Amount: Low to High</option>
						</select>
					</div>
				</div>

				<!-- Sales Orders List -->
				<div id="salesOrdersContainer" class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
					@php
						$filteredSalesOrders = $salesOrders->filter(fn($order) => $order->remaining_balance > 0)->shuffle();
					@endphp
					@forelse($filteredSalesOrders as $salesOrder)
						<div onclick="selectTransaction('{{ $salesOrder->order_number }}', {{ $salesOrder->total_amount }}, '{{ \Carbon\Carbon::parse($salesOrder->order_date)->format('F d, Y') }}', 'Income', {{ $salesOrder->id }}, {{ $salesOrder->remaining_balance }})" 
                             data-amount="{{ $salesOrder->remaining_balance }}" 
                             data-random="{{ rand() }}"
                             class="transaction-item p-4 bg-white border-2 border-gray-300 rounded-xl hover:border-green-500 hover:bg-green-50 cursor-pointer transition-all shadow-md hover:shadow-lg">
							<div class="flex justify-between items-start">
								<div class="flex-1">
									<h3 class="font-bold text-gray-800 text-lg">{{ $salesOrder->order_number }}</h3>
									<p class="text-sm text-gray-600 font-medium mt-1">{{ $salesOrder->customer->name ?? 'N/A' }}</p>
									<p class="text-xs text-gray-500 mt-1">{{ $salesOrder->notes ?? 'Sales Order' }}</p>
								</div>
								<div class="flex flex-col items-end gap-2">
									<span class="px-3 py-1.5 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-bold rounded-lg shadow-lg">
										₱{{ number_format($salesOrder->total_amount, 2) }}
									</span>
									<span class="px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded-lg">
										Balance: ₱{{ number_format($salesOrder->remaining_balance, 2) }}
									</span>
								</div>
							</div>
							<div class="mt-3 pt-3 border-t border-gray-200">
								<p class="text-xs text-gray-500">Order Date: {{ \Carbon\Carbon::parse($salesOrder->order_date)->format('F d, Y') }}</p>
							</div>
						</div>
					@empty
						<div class="py-12 text-center">
							<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
							</svg>
							<p class="text-gray-500">No pending sales orders available</p>
						</div>
					@endforelse
				</div>

				<!-- Purchase Orders List -->
				<div id="purchaseOrdersContainer" class="space-y-3 max-h-80 overflow-y-auto pr-2 hidden custom-scrollbar">
					@php
						$filteredPurchaseOrders = $purchaseOrders->filter(fn($order) => $order->remaining_balance > 0)->shuffle();
					@endphp
					@forelse($filteredPurchaseOrders as $purchaseOrder)
						<div onclick="selectTransaction('{{ $purchaseOrder->order_number }}', {{ $purchaseOrder->total_amount }}, '{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('F d, Y') }}', 'Expense', {{ $purchaseOrder->id }}, {{ $purchaseOrder->remaining_balance }})" 
                             data-amount="{{ $purchaseOrder->remaining_balance }}" 
                             data-random="{{ rand() }}"
                             class="transaction-item p-4 bg-white border-2 border-gray-300 rounded-xl hover:border-red-500 hover:bg-red-50 cursor-pointer transition-all shadow-md hover:shadow-lg">
							<div class="flex justify-between items-start">
								<div class="flex-1">
									<h3 class="font-bold text-gray-800 text-lg">{{ $purchaseOrder->order_number }}</h3>
									<p class="text-sm text-gray-600 font-medium mt-1">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
									<p class="text-xs text-gray-500 mt-1">{{ $purchaseOrder->notes ?? 'Purchase Order' }}</p>
								</div>
								<div class="flex flex-col items-end gap-2">
									<span class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-bold rounded-lg shadow-lg">
										₱{{ number_format($purchaseOrder->total_amount, 2) }}
									</span>
									<span class="px-2 py-1 bg-yellow-600 text-white text-xs font-semibold rounded-lg">
										Balance: ₱{{ number_format($purchaseOrder->remaining_balance, 2) }}
									</span>
								</div>
							</div>
							<div class="mt-3 pt-3 border-t border-gray-200">
								<p class="text-xs text-gray-500">Order Date: {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('F d, Y') }}</p>
							</div>
						</div>
					@empty
						<div class="py-12 text-center">
							<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
							</svg>
							<p class="text-gray-500">No pending purchase orders available</p>
						</div>
					@endforelse
				</div>

				<!-- Confirmation Form -->
				<form id="transactionForm" method="POST" action="{{ route('accounting.transaction.store') }}">
					@csrf
					<input type="hidden" name="reference" id="formRef">
					<input type="hidden" name="date" id="formDate">
					<input type="hidden" name="transaction_type" id="formType">
					<input type="hidden" name="order_id" id="formOrderId">
					<input type="hidden" name="total_amount" id="formTotalAmount">
					
					<div id="confirmationSection" class="hidden mt-6 p-5 bg-white rounded-xl border-2 border-amber-500 shadow-xl">
						<h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
							<svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
							</svg>
							Confirm Transaction Details
						</h3>
						<div class="grid grid-cols-2 gap-4 mb-5">
							<div class="bg-gray-100 p-3 rounded-lg">
								<span class="text-gray-600 text-xs font-medium">Reference</span>
								<p id="confirmRef" class="font-bold text-gray-800 text-sm mt-1">-</p>
							</div>
							<div class="bg-gray-100 p-3 rounded-lg">
								<span class="text-gray-600 text-xs font-medium">Total Amount</span>
								<p id="confirmAmount" class="font-bold text-gray-800 text-sm mt-1">-</p>
							</div>
							<div class="bg-gray-100 p-3 rounded-lg">
								<span class="text-gray-600 text-xs font-medium">Date</span>
								<p id="confirmDate" class="font-bold text-gray-800 text-sm mt-1">-</p>
							</div>
							<div class="bg-gray-100 p-3 rounded-lg">
								<span class="text-gray-600 text-xs font-medium">Type</span>
								<p id="confirmType" class="font-bold text-gray-800 text-sm mt-1">-</p>
							</div>
						</div>
						<div class="mb-5">
							<label class="block text-sm font-bold text-gray-800 mb-2">Payment Amount</label>
							<input type="text" id="paymentAmount" name="amount" class="w-full bg-white border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter payment amount" required>
							<p class="text-xs text-gray-600 mt-2">Maximum allowed: <span id="maxAmount" class="font-bold text-amber-600">-</span></p>
						</div>
						<div class="flex gap-3">
							<button type="button" onclick="resetSelection()" class="flex-1 px-4 py-2.5 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
								Back
							</button>
						<button type="button" onclick="confirmTransaction(event)" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg hover:from-amber-600 hover:to-orange-700 transition shadow-lg font-semibold">
							Confirm Payment
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Confirmation Modal -->
<div id="confirmTransactionModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[100001]" style="display: none; align-items: center; justify-content: center;">
	<div class="bg-amber-50 rounded-xl shadow-2xl w-full max-w-md mx-4 border-4 border-slate-700 z-[100003]">
		<!-- Modal Header with Gradient -->
		<div class="sticky top-0 z-[100002] bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 rounded-t-lg">
			<h3 class="text-xl font-bold text-white flex items-center gap-2">
				<svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
				</svg>
				Confirm Transaction
			</h3>
		</div>
		
		<!-- Modal Body -->
		<div class="px-6 py-5 space-y-4">
			<p class="text-gray-700 text-sm leading-relaxed">
				Are you sure you want to record this transaction?
			</p>
			
			<div class="bg-white p-4 rounded-lg border-2 border-slate-200 space-y-2">
				<div class="flex justify-between">
					<span class="text-gray-600 text-sm font-medium">Reference:</span>
					<span id="modalRef" class="text-gray-900 font-semibold text-sm">-</span>
				</div>
				<div class="flex justify-between">
					<span class="text-gray-600 text-sm font-medium">Type:</span>
					<span id="modalType" class="font-semibold text-sm">-</span>
				</div>
				<div class="flex justify-between">
					<span class="text-gray-600 text-sm font-medium">Payment Amount:</span>
					<span id="modalAmount" class="text-gray-900 font-bold text-sm">-</span>
				</div>
			</div>
		</div>
		
		<!-- Modal Footer -->
		<div class="px-6 pb-5 flex gap-3">
			<button type="button" onclick="closeConfirmTransaction()" 
				class="flex-1 px-4 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition shadow-md">
				Cancel
			</button>
			<button type="button" onclick="submitConfirmedTransaction()" 
				class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-semibold transition shadow-lg flex items-center justify-center gap-2">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
				</svg>
				Yes, Confirm
			</button>
		</div>
	</div>
</div>

	<script>
		let maxPaymentAmount = 0;

		// Filter functionality
		function filterTransactions() {
			const searchInput = document.getElementById('searchInput').value.toLowerCase();
			const categoryFilter = document.getElementById('categoryFilter').value;
			const statusFilter = document.getElementById('statusFilter').value;
			const rows = document.querySelectorAll('.transaction-row');
			
			rows.forEach(row => {
				const rowType = row.getAttribute('data-type');
				const rowStatus = row.getAttribute('data-status');
				const rowId = row.getAttribute('data-id').toLowerCase();
				const rowDate = row.getAttribute('data-date').toLowerCase();
				const rowCategory = row.getAttribute('data-category').toLowerCase();
				const rowDescription = row.getAttribute('data-description').toLowerCase();
				
				const categoryMatch = categoryFilter === 'all' || rowType === categoryFilter;
				const statusMatch = statusFilter === 'all' || 
									rowStatus === statusFilter || 
									(statusFilter === 'unpaid' && rowStatus === 'pending');
				const searchMatch = searchInput === '' || 
					rowId.includes(searchInput) ||
					rowDate.includes(searchInput) ||
					rowType.toLowerCase().includes(searchInput) ||
					rowCategory.includes(searchInput) ||
					rowDescription.includes(searchInput);
				
				if (categoryMatch && statusMatch && searchMatch) {
					row.style.display = '';
				} else {
					row.style.display = 'none';
				}
			});
		}

		// Attach filter event listeners
		document.getElementById('searchInput').addEventListener('input', filterTransactions);
		document.getElementById('categoryFilter').addEventListener('change', filterTransactions);
		document.getElementById('statusFilter').addEventListener('change', filterTransactions);

		// Modal functions
		function openAddTransaction() {
			document.getElementById('addTransactionModal').classList.remove('hidden');
			document.getElementById('confirmationSection').classList.add('hidden');
			document.getElementById('salesOrdersContainer').classList.remove('hidden');
			document.getElementById('purchaseOrdersContainer').classList.add('hidden');
			// Reset tab styling
			showSalesOrders();
			document.body.style.overflow = 'hidden';
		}

		function closeAddTransaction() {
			document.getElementById('addTransactionModal').classList.add('hidden');
			resetSelection();
			document.body.style.overflow = '';
		}

		// Tab switching
		function showSalesOrders() {
			document.getElementById('salesOrdersContainer').classList.remove('hidden');
			document.getElementById('purchaseOrdersContainer').classList.add('hidden');
			// Update tab styling for sales orders
			document.getElementById('salesOrdersTab').classList.add('bg-amber-500', 'text-white', 'shadow-lg');
			document.getElementById('salesOrdersTab').classList.remove('bg-slate-600', 'text-slate-300');
			document.getElementById('purchaseOrdersTab').classList.add('bg-slate-600', 'text-slate-300');
			document.getElementById('purchaseOrdersTab').classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
			document.getElementById('confirmationSection').classList.add('hidden');
			filterModalItems(); // Re-apply filters
		}

		function showPurchaseOrders() {
			document.getElementById('purchaseOrdersContainer').classList.remove('hidden');
			document.getElementById('salesOrdersContainer').classList.add('hidden');
			// Update tab styling for purchase orders
			document.getElementById('purchaseOrdersTab').classList.add('bg-amber-500', 'text-white', 'shadow-lg');
			document.getElementById('purchaseOrdersTab').classList.remove('bg-slate-600', 'text-slate-300');
			document.getElementById('salesOrdersTab').classList.add('bg-slate-600', 'text-slate-300');
			document.getElementById('salesOrdersTab').classList.remove('bg-amber-500', 'text-white', 'shadow-lg');
			document.getElementById('confirmationSection').classList.add('hidden');
			filterModalItems(); // Re-apply filters
		}

		// Modal Filtering and Sorting Logic
		function filterModalItems() {
			const searchTerm = document.getElementById('modalSearchInput').value.toLowerCase();
			const sortOrder = document.getElementById('modalSortFilter').value;
			
			const isSalesActive = !document.getElementById('salesOrdersContainer').classList.contains('hidden');
			const container = isSalesActive 
				? document.getElementById('salesOrdersContainer') 
				: document.getElementById('purchaseOrdersContainer');
				
			if (!container) return;

			// Get all transaction items
			const items = Array.from(container.querySelectorAll('.transaction-item'));
			
			// 1. Filter items
			items.forEach(item => {
				const orderNum = item.querySelector('h3')?.textContent.toLowerCase() || '';
				const name = item.querySelector('.text-sm')?.textContent.toLowerCase() || '';
				const matchesSearch = orderNum.includes(searchTerm) || name.includes(searchTerm);
				
				if (matchesSearch) {
					item.classList.remove('hidden');
				} else {
					item.classList.add('hidden');
				}
			});

			// 2. Sort items
			const sortedItems = items.sort((a, b) => {
				if (sortOrder === 'high-low') {
					return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
				} else if (sortOrder === 'low-high') {
					return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
				} else if (sortOrder === 'random') {
					return parseInt(a.dataset.random) - parseInt(b.dataset.random);
				}
				return 0;
			});

			// 3. Re-append sorted items to container
			sortedItems.forEach(item => container.appendChild(item));
		}

		// Attach listeners
		document.getElementById('modalSearchInput').addEventListener('input', filterModalItems);
		document.getElementById('modalSortFilter').addEventListener('change', filterModalItems);

		// Transaction selection
		function selectTransaction(reference, amount, date, type, orderId, remainingBalance = null) {
			document.getElementById('confirmRef').textContent = reference;
			document.getElementById('confirmAmount').textContent = '₱' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
			document.getElementById('confirmDate').textContent = date;
			document.getElementById('confirmType').textContent = type;
			
			// Use remaining balance for both Expense and Income types, fall back to full amount if not available
			const paymentCap = remainingBalance !== null ? remainingBalance : amount;
			
			// Store max amount for validation
			maxPaymentAmount = paymentCap;
			
			// Set hidden form fields
			document.getElementById('formRef').value = reference;
			document.getElementById('formTotalAmount').value = amount;
			document.getElementById('formDate').value = date;
			document.getElementById('formType').value = type;
			document.getElementById('formOrderId').value = orderId;
			document.getElementById('paymentAmount').value = paymentCap.toLocaleString('en-US');
			
			// Display max amount with formatting
			document.getElementById('maxAmount').textContent = '₱' + paymentCap.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
			
			// Hide transaction list and show confirmation
			document.getElementById('salesOrdersContainer').classList.add('hidden');
			document.getElementById('purchaseOrdersContainer').classList.add('hidden');
			document.getElementById('confirmationSection').classList.remove('hidden');
		}

		// Format payment amount input with commas and enforce max limit
		document.getElementById('paymentAmount').addEventListener('input', function(e) {
			let value = this.value.replace(/,/g, '');
			let numValue = parseInt(value) || 0;
			
			// Enforce max limit
			if (numValue > maxPaymentAmount) {
				numValue = maxPaymentAmount;
			}
			
			if (numValue > 0) {
				this.value = numValue.toLocaleString('en-US');
			} else {
				this.value = '';
			}
		});

		let isConfirming = false;

		// Handle confirmation modal
		function confirmTransaction(event) {
			if (event) event.preventDefault();
			
			let paymentAmount = document.getElementById('paymentAmount').value.replace(/,/g, '');
			let numValue = parseInt(paymentAmount) || 0;
			
			// Validate payment amount
			if (numValue <= 0) {
				alert('Please enter a valid payment amount');
				return false;
			}
			
			if (numValue > maxPaymentAmount) {
				alert('Payment amount cannot exceed ₱' + maxPaymentAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				return false;
			}
			
			// Populate modal with transaction details
			const reference = document.getElementById('confirmRef').textContent;
			const type = document.getElementById('confirmType').textContent;
			const formattedAmount = '₱' + numValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
			
			document.getElementById('modalRef').textContent = reference;
			document.getElementById('modalType').textContent = type;
			document.getElementById('modalType').className = type === 'Income' 
				? 'font-semibold text-sm text-green-600' 
				: 'font-semibold text-sm text-red-600';
			document.getElementById('modalAmount').textContent = formattedAmount;
			
			// Show modal
			const modal = document.getElementById('confirmTransactionModal');
			modal.classList.remove('hidden');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
			
			return false;
		}
		
		function closeConfirmTransaction() {
			const modal = document.getElementById('confirmTransactionModal');
			modal.classList.add('hidden');
			modal.style.display = 'none';
			document.body.style.overflow = '';
			isConfirming = false;
		}
		
		function submitConfirmedTransaction() {
			isConfirming = true;
			
			// Remove commas from payment amount before submission
			let paymentAmount = document.getElementById('paymentAmount').value.replace(/,/g, '');
			document.getElementById('paymentAmount').value = paymentAmount;
			
			// Close modal
			closeConfirmTransaction();
			
			// Submit the form
			document.getElementById('transactionForm').submit();
		}

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

		@if (session('success'))
			document.addEventListener('DOMContentLoaded', function() {
				showSuccessNotification('{{ session('success') }}');
			});
		@endif
		@if (session('error'))
			document.addEventListener('DOMContentLoaded', function() {
				showErrorNotification('{{ session('error') }}');
			});
		@endif

		// Close modal with Escape key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				const modal = document.getElementById('addTransactionModal');
				if (modal && !modal.classList.contains('hidden')) {
					closeAddTransaction();
				}
			}
		});

		function resetSelection() {
			document.getElementById('confirmationSection').classList.add('hidden');
			document.getElementById('salesOrdersContainer').classList.remove('hidden');
			document.getElementById('purchaseOrdersContainer').classList.add('hidden');
		}

		// Removed modal click listener since we're using inline section now

	// Export Dropdown Toggle
	const exportButtonAccounting = document.getElementById('exportButtonAccounting');
	const exportDropdownAccounting = document.getElementById('exportDropdownAccounting');

	if (exportButtonAccounting && exportDropdownAccounting) {
		exportButtonAccounting.addEventListener('click', function(e) {
			e.stopPropagation();
			const isHidden = exportDropdownAccounting.classList.contains('hidden');
			
			if (isHidden) {
				// Position the dropdown below the button
				const rect = exportButtonAccounting.getBoundingClientRect();
				exportDropdownAccounting.style.top = (rect.bottom + 8) + 'px';
				exportDropdownAccounting.style.right = (window.innerWidth - rect.right) + 'px';
				exportDropdownAccounting.classList.remove('hidden');
			} else {
				exportDropdownAccounting.classList.add('hidden');
			}
		});

		// Close dropdown when clicking outside
		document.addEventListener('click', function(e) {
			if (!exportButtonAccounting.contains(e.target) && !exportDropdownAccounting.contains(e.target)) {
				exportDropdownAccounting.classList.add('hidden');
			}
		});
	}

	// Add row selection functionality for transactions
	const transactionTable = document.querySelector('.transaction-row');
	if (transactionTable) {
		document.addEventListener('click', function(e) {
			const row = e.target.closest('tr.transaction-row');
			
			if (row) {
				// Remove selection from all rows
				const allRows = document.querySelectorAll('tr.transaction-row');
				allRows.forEach(r => {
					r.classList.remove('!bg-gray-600', 'border-l-4', 'border-amber-500');
				});
				
				// Add selection to clicked row using Tailwind classes
				row.classList.add('!bg-gray-600', 'border-l-4', 'border-amber-500');
			}
		});
	}

	// Tab switching functionality
	function showAccountingTab(tab) {
		const transactionTab = document.getElementById('transactionTab');
		const reportsTab = document.getElementById('reportsTab');
		const transactionContent = document.getElementById('transactionContent');
		const reportsContent = document.getElementById('reportsContent');
		
		if (tab === 'transaction') {
			transactionTab.style.backgroundColor = '#FFF1DA';
			transactionTab.style.color = '#111827';
			transactionTab.style.borderColor = '#FDE68A';
			reportsTab.style.backgroundColor = '#475569';
			reportsTab.style.color = '#FFFFFF';
			reportsTab.style.borderColor = '#64748b';
			transactionContent.classList.remove('hidden');
			reportsContent.classList.add('hidden');
		} else if (tab === 'reports') {
			reportsTab.style.backgroundColor = '#FFF1DA';
			reportsTab.style.color = '#111827';
			reportsTab.style.borderColor = '#FDE68A';
			transactionTab.style.backgroundColor = '#475569';
			transactionTab.style.color = '#FFFFFF';
			transactionTab.style.borderColor = '#64748b';
			transactionContent.classList.add('hidden');
			reportsContent.classList.remove('hidden');
		}
	}

	// Export Functions
	function exportTransactionReceipt(event) {
		event.preventDefault();
		exportDropdownAccounting.classList.add('hidden');
		
		// Get the selected transaction from the table
		const selectedRow = document.querySelector('tr.transaction-row.border-amber-500');
		
		if (!selectedRow) {
			showErrorNotification('Please select a transaction by clicking on a row in the table to export as receipt.');
			return;
		}
		
		// Extract transaction ID from the selected row
		const transactionId = selectedRow.getAttribute('data-id');
		
		// Open receipt in new tab
		window.open(`/accounting/receipt/${transactionId}`, '_blank');
	}

	function exportFinancialReport(event) {
		event.preventDefault();
		exportDropdownAccounting.classList.add('hidden');
		// Implement financial report export (PDF)
		window.location.href = '/accounting/export/financial-report';
	}

	function exportTransactionHistory(event) {
		event.preventDefault();
		exportDropdownAccounting.classList.add('hidden');
		// Implement transaction history export (CSV)
		window.location.href = '/accounting/export/transactions';
	}
	</script>
@endsection