@extends('layouts.system')

@section('main-content')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
	<!-- Header Section -->
	<div class="p-5 bg-amber-50 border-b border-amber-200 relative z-10">
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-xl font-bold text-gray-800">Inventory Management</h1>
				<p class="text-xs font-medium text-gray-600 mt-2">Track and manage raw materials and finished products</p>
			</div>
			<div class="flex space-x-3">
				@php
					$pendingReceiveCount = \App\Models\PurchaseOrder::where('status', '!=', 'received')->count();
				@endphp
				<button onclick="openReceiveStockModal()" class="relative px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center gap-2 font-medium text-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
					</svg>
					Receive Stock
					@if($pendingReceiveCount > 0)
						<span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white shadow-sm flex items-center justify-center min-w-[20px] h-5">
							{{ $pendingReceiveCount }}
						</span>
					@endif
				</button>
				<button onclick="openStockLogsModal()" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-all shadow-lg hover:shadow-xl flex items-center gap-2 font-medium text-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
					</svg>
					Stock Logs
				</button>
			</div>
		</div>
	</div>
    <div class="flex-1 p-5 bg-amber-50 overflow-y-auto">

			<!-- Metrics Cards -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6 px-5">
				<!-- Total Items Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Total Materials</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ $totalMaterials ?? 10 }}</p>
							<p class="text-slate-300 text-xs font-medium mt-1">{{ $totalMaterials ?? 10 }} Items</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.package', ['class' => 'w-5 h-5 text-amber-400'])
						</div>
					</div>
					<div class="mt-2 pt-2 border-t border-slate-600/50">
						<div class="flex items-center justify-between text-xs text-slate-400">
							<span>Inventory Status</span>
							<span class="text-green-400 font-semibold">Active</span>
						</div>
					</div>
				</div>

				<!-- Low Stock Alerts Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Low Stock Alerts</p>
							<p class="text-2xl font-bold mt-2 {{ ($lowStockAlerts ?? 3) > 4 ? 'text-red-400' : 'bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent' }}">{{ $lowStockAlerts ?? 3 }}</p>
							<p class="text-xs font-medium mt-1 {{ ($lowStockAlerts ?? 3) > 4 ? 'text-red-300' : 'text-slate-300' }}">{{ $lowStockAlerts ?? 3 }} Items</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.cart', ['class' => ($lowStockAlerts ?? 3) > 4 ? 'w-5 h-5 text-red-400 animate-pulse' : 'w-5 h-5 text-amber-400'])
						</div>
					</div>
					<div class="mt-2 pt-2 border-t border-slate-600/50">
						<div class="w-full bg-slate-600 rounded-full h-1.5 overflow-hidden">
							<div class="{{ ($lowStockAlerts ?? 3) > 4 ? 'bg-red-500' : 'bg-amber-500' }} h-1.5 rounded-full transition-all duration-500" 
								 style="width: {{ min((($lowStockAlerts ?? 3) / max($totalMaterials ?? 10, 1)) * 100, 100) }}%"></div>
						</div>
					</div>
				</div>

				<!-- New Order Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">New Orders</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ $newOrders ?? 2 }}</p>
							<p class="text-slate-300 text-xs font-medium mt-1">Finished products</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.cart', ['class' => 'w-5 h-5 text-blue-400'])
						</div>
					</div>
					<div class="mt-2 pt-2 border-t border-slate-600/50">
						<div class="flex items-center justify-between text-xs text-slate-400">
							<span>This week</span>
							<span class="text-blue-400 font-semibold">+{{ $newOrders ?? 2 }}</span>
						</div>
					</div>
				</div>

				<!-- Pending Deliveries Card -->
				<div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
					<div class="flex justify-between items-start">
						<div>
							<p class="text-slate-300 text-xs font-semibold uppercase tracking-wide">Pending Deliveries</p>
							<p class="text-2xl font-bold mt-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ $pendingDeliveries ?? 3 }}</p>
							<p class="text-slate-300 text-xs font-medium mt-1">Awaiting delivery</p>
						</div>
						<div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
							@include('components.icons.cart', ['class' => 'w-5 h-5 text-purple-400'])
						</div>
					</div>
					<div class="mt-2 pt-2 border-t border-slate-600/50">
						<div class="flex items-center justify-between text-xs text-slate-400">
							<span>Purchase orders</span>
							<span class="text-purple-400 font-semibold">{{ $pendingDeliveries ?? 3 }} PO</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Main Content Container (with Padding) -->
			<div class="px-5 pb-5">
				<!-- Inventory Items Section -->
				<section class="bg-gradient-to-br from-slate-700 to-slate-800 text-white p-3 rounded-xl overflow-visible shadow-xl border border-slate-600">
					<header class="flex justify-between items-center mb-6">
						<div>
							<h2 class="text-xl font-bold text-white">Inventory Items</h2>
							<p class="text-slate-300 text-xs font-medium mt-2">Manage raw materials from suppliers and finished products</p>
						</div>
						<div id="materialsButton" class="flex space-x-2">
							<button onclick="openAddItemModal()" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-md flex items-center gap-1.5 font-medium">
								<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
								</svg>
								<span>Add Item</span>
							</button>
						</div>
						<div id="productsButton" class="flex space-x-2 hidden">
							<button onclick="openAddProductModal()" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-md flex items-center gap-1.5 font-medium">
								<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
								</svg>
								<span>Add Product</span>
							</button>
						</div>
					</header>

					<!-- Search + Filters -->
					@php
						$inventoryCategories = collect($materials ?? [])->pluck('category')->filter()->unique()->values();
					@endphp
					<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
						<div class="flex-1 max-w-md">
							<div class="relative">
								<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
									<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
									</svg>
								</div>
								<input type="search" id="searchInput" placeholder="Search items..." class="w-full pl-10 pr-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-white placeholder-slate-400">
							</div>
						</div>
						<div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
							<select id="categoryFilter" class="bg-slate-700 border-slate-600 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 w-full sm:w-auto">
								<option value="">All Categories</option>
								@foreach($inventoryCategories as $category)
									<option value="{{ $category }}">{{ $category }}</option>
								@endforeach
								<option value="Products">Products</option>
							</select>
						</div>
					</div>

                <!-- Tabs -->
                <div class="flex space-x-2 w-full mb-6">
                    <button onclick="showTab('materials')" id="materials-tab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #FFF1DA; border-color: #FDE68A; color: #111827;">Materials</button>
                    <button onclick="showTab('products')" id="products-tab" class="flex-1 px-5.5 py-3 rounded-xl border-2 font-bold text-sm transition-all shadow-lg" style="background-color: #475569; border-color: #64748b; color: #FFFFFF;">Products</button>
                </div>

                <!-- Materials Table -->
                <div id="materials-table" class="space-y-3 overflow-y-auto custom-scrollbar" style="max-height:60vh;">
                    @forelse($materials ?? [] as $material)
                    <div class="p-4 border-2 border-slate-600 rounded-xl hover:border-amber-500 hover:bg-slate-600/50 transition-all shadow-lg hover:shadow-xl backdrop-blur-sm cursor-pointer" data-name="{{ $material->name }}" data-category="{{ $material->category }}" onclick="openStockModal('material', {{ $material->id }})">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-bold text-white text-lg">{{ $material->name }}</h3>
                                <p class="text-sm text-slate-300 font-medium mt-1">{{ $material->supplier->name ?? 'N/A' }} • {{ $material->unit }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($material->isLowStock())
                                    <span class="px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-xl shadow-lg">Low Stock</span>
                                @else
                                    <span class="px-3 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-bold rounded-xl shadow-lg">In Stock</span>
                                @endif
                                <span class="text-white font-bold text-lg">₱{{ number_format($material->unit_cost, 2) }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2 text-sm">
                            <div>
                                <span class="text-slate-400 font-medium text-xs">Current Stock</span>
                                <p class="text-white font-bold text-lg mt-1">{{ $material->current_stock }} {{ $material->unit }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium text-xs">Min Stock</span>
                                <p class="text-white font-bold text-lg mt-1">{{ $material->minimum_stock }} {{ $material->unit }}</p>
                            </div>
                            <div class="flex items-center space-x-2 justify-end">
                                <button onclick="event.stopPropagation(); openDeleteModal('material', {{ $material->id }}, '{{ addslashes($material->name) }}')" class="p-2.5 hover:bg-red-500/20 rounded-lg transition-all group" title="Delete">
                                    <svg class="w-5 h-5 text-red-400 group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 px-3 text-center">
                        <svg class="w-16 h-16 text-slate-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-slate-300 text-lg font-medium">No materials found</p>
                        <p class="text-slate-400 text-xs mt-1.5">Add your first material to get started</p>
                    </div>
                    @endforelse
                </div>

                <!-- Products Table -->
                <div id="products-table" class="space-y-3 overflow-y-auto custom-scrollbar hidden" style="max-height:60vh;">
                    @forelse($products ?? [] as $product)
                    <div class="p-4 border-2 border-slate-600 rounded-xl hover:border-amber-500 hover:bg-slate-600/50 transition-all shadow-lg hover:shadow-xl backdrop-blur-sm cursor-pointer" data-name="{{ $product->product_name }}" data-category="Products" onclick="openStockModal('product', {{ $product->id }})">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-bold text-white text-lg">{{ $product->product_name }}</h3>
                                <p class="text-sm text-slate-300 font-medium mt-1">Finished Product</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-white font-bold text-lg">₱{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2 text-sm">
                            <div>
                                <span class="text-slate-400 font-medium text-xs">Production Cost</span>
                                <p class="text-white font-bold text-lg mt-1">₱{{ number_format($product->production_cost, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-medium text-xs">Selling Price</span>
                                <p class="text-white font-bold text-lg mt-1">₱{{ number_format($product->selling_price, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 mt-2 justify-end">
                            <button onclick="event.stopPropagation(); openEditProductModal({{ $product->id }})" class="p-2.5 hover:bg-slate-500 rounded-lg transition-all group" title="Edit">
                                <svg class="w-5 h-5 text-amber-400 group-hover:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                            </button>
                            <button onclick="event.stopPropagation(); openDeleteModal('product', {{ $product->id }}, '{{ addslashes($product->product_name) }}')" class="p-2.5 hover:bg-red-500/20 rounded-lg transition-all group" title="Delete">
                                <svg class="w-5 h-5 text-red-400 group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 px-3 text-center">
                        <svg class="w-16 h-16 text-slate-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-slate-300 text-lg font-medium">No products found</p>
                        <p class="text-slate-400 text-xs mt-1.5">Add your first product to get started</p>
                    </div>
                    @endforelse
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

        <!-- Add Item Modal (for Materials) -->
        <div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700" style="background-color: #FFF1DA;">
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                            <h3 class="text-xl font-bold text-gray-900">Add New Material</h3>
                            <button onclick="closeAddItemModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form id="addItemForm" method="POST" action="{{ route('inventory.store') }}">
                            @csrf
                            <input type="hidden" name="type" value="material">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Material Name *</label>
                                    <input type="text" name="name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Category *</label>
                                    <div class="grid grid-cols-2 gap-2" id="materialCategoryButtons">
                                        @foreach(['lumber', 'hardware', 'finishing', 'tools'] as $cat)
                                            <button type="button" 
                                                onclick="selectMaterialCategory(this, '{{ $cat }}')"
                                                class="category-btn px-3 py-2 border-2 border-gray-300 rounded-xl text-xs font-bold text-gray-600 hover:border-amber-500 hover:bg-amber-50 transition-all capitalize">
                                                {{ $cat }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="category" id="selectedMaterialCategory" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Unit *</label>
                                    <input type="text" name="unit" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Current Stock *</label>
                                    <input type="number" name="current_stock" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Minimum Stock *</label>
                                    <input type="number" name="minimum_stock" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Unit Cost *</label>
                                    <input type="number" name="unit_cost" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Supplier *</label>
                                    <div class="bg-white/50 border-2 border-gray-300 rounded-2xl p-4 shadow-inner">
                                        <div class="relative mb-3">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                            <input type="text" id="supplierSearchInput" placeholder="Search supplier..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm outline-none transition-all">
                                        </div>
                                        <div id="supplierButtonsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                            @foreach($suppliers ?? [] as $supplier)
                                                <button type="button" 
                                                    onclick="selectSupplier(this, {{ $supplier->id }})"
                                                    data-supplier-name="{{ strtolower($supplier->name) }}"
                                                    class="supplier-btn group p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-amber-500 hover:bg-amber-50 transition-all shadow-sm text-left">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-amber-500 transition-colors"></div>
                                                        <span class="text-xs font-bold text-gray-700">{{ $supplier->name }}</span>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="supplier_id" id="selectedMaterialSupplierId" required>
                                    </div>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Description</label>
                                    <textarea name="description" rows="3" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all"></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2 mt-6">
                                <button type="button" onclick="closeAddItemModal()" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">
                                    Add Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div id="addProductModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700" style="background-color: #FFF1DA;">
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                            <h3 class="text-xl font-bold text-gray-900">Add New Product</h3>
                            <button onclick="closeAddProductModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form id="addProductForm" method="POST" action="{{ route('inventory.store') }}">
                            @csrf
                            <input type="hidden" name="type" value="product">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Product Name *</label>
                                    <input type="text" name="product_name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Unit *</label>
                                    <input type="text" name="unit" value="pieces" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Category *</label>
                                    <div class="grid grid-cols-2 gap-2" id="productCategoryButtons">
                                        @foreach(['Products', 'Custom', 'Furniture', 'Others'] as $cat)
                                            <button type="button" 
                                                onclick="selectProductCategory(this, '{{ $cat }}')"
                                                class="prod-category-btn px-3 py-2 border-2 border-gray-300 rounded-xl text-xs font-bold text-gray-600 hover:border-amber-500 hover:bg-amber-50 transition-all capitalize {{ $cat === 'Products' ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500/20' : '' }}">
                                                {{ $cat }}
                                            </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="category" id="selectedProductCategory" value="Products" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Production Cost *</label>
                                    <input type="number" name="production_cost" id="productProductionCost" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Selling Price *</label>
                                    <input type="number" name="selling_price" id="productSellingPrice" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                            </div>

                            <!-- Materials Section -->
                            <div class="mt-6 pt-6 border-t-2 border-gray-300">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-bold text-gray-900">Materials Needed</h4>
                                    <button type="button" onclick="addMaterialRow()" class="px-3 py-2 bg-green-600 text-white text-xs rounded-xl hover:bg-green-700 font-bold shadow-lg transition-all">
                                        + Add Material
                                    </button>
                                </div>
                                
                                <div id="materialsContainer" class="space-y-3">
                                    <!-- Material rows will be added here -->
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2 mt-6">
                                <button type="button" onclick="closeAddProductModal()" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">
                                    Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editProductModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border-2 border-slate-700" style="background-color: #FFF1DA;">
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-slate-700">
                            <h3 class="text-xl font-bold text-gray-900">Edit Product</h3>
                            <button onclick="closeEditProductModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form id="editProductForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="product">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Product Name *</label>
                                    <input type="text" id="editProductName" name="product_name" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Unit *</label>
                                    <input type="text" id="editProductUnit" name="unit" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Category *</label>
                                    <input type="text" id="editProductCategory" name="category" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Production Cost *</label>
                                    <input type="number" id="editProductProductionCost" name="production_cost" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-3">Selling Price *</label>
                                    <input type="number" id="editProductSellingPrice" name="selling_price" step="0.01" min="0" class="w-full border-2 border-gray-300 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition-all" required>
                                </div>
                            </div>

                            <!-- Materials Section -->
                            <div class="mt-6 pt-6 border-t-2 border-gray-300">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-bold text-gray-900">Materials Needed</h4>
                                    <button type="button" onclick="addEditMaterialRow()" class="px-3 py-2 bg-green-600 text-white text-xs rounded-xl hover:bg-green-700 font-bold shadow-lg transition-all">
                                        + Add Material
                                    </button>
                                </div>
                                
                                <div id="editMaterialsContainer" class="space-y-3">
                                    <!-- Material rows will be added here -->
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2 mt-6">
                                <button type="button" onclick="closeEditProductModal()" class="px-6 py-1.5 border-2 border-gray-300 rounded-xl text-gray-700 font-bold text-sm hover:bg-gray-100 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold text-sm rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all">
                                    Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock View Modal -->
        <div id="stockModal" class="modal-overlay fixed inset-0 bg-black/60 backdrop-blur-sm hidden overflow-y-auto flex items-center justify-center z-50" onclick="if(event.target === this) closeStockModal()">
            <div id="stockModalContent" class="modal-content bg-amber-50 rounded-xl max-w-4xl w-[92%] my-6 max-h-[80vh] overflow-y-auto shadow-2xl transform transition-all border-2 border-slate-700" onclick="event.stopPropagation()">
                <div class="p-2">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-5 border-b-2 pb-6" style="border-color: #374151;">
                        <div>
                            <h3 class="text-xl font-bold" style="color: #374151;">Item Details & Materials Needed</h3>
                            <p id="itemName" class="text-sm mt-1.5 font-medium" style="color: #666;"></p>
                        </div>
                        <button onclick="closeStockModal()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-xl p-2 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Item Info Cards -->
                    <div id="itemInfoGrid" class="grid grid-cols-3 gap-4 mb-5">
                        <div id="currentStockCard" class="p-4 rounded-xl border-l-4 shadow-lg" style="background-color: rgba(255,255,255,0.7); border-left-color: #374151;">
                            <p class="text-sm font-semibold" style="color: #374151;">Current Stock</p>
                            <p id="currentStock" class="text-xl font-bold mt-1.5" style="color: #374151;">-</p>
                        </div>
                        <div id="minimumStockCard" class="p-4 rounded-xl border-l-4 shadow-lg" style="background-color: rgba(255,255,255,0.7); border-left-color: #F57C00;">
                            <p class="text-sm font-semibold" style="color: #E65100;">Minimum Stock</p>
                            <p id="minimumStock" class="text-xl font-bold mt-1.5" style="color: #E65100;">-</p>
                        </div>
                        <div id="unitCostCard" class="p-4 rounded-xl border-l-4 shadow-lg" style="background-color: rgba(255,255,255,0.7); border-left-color: #388E3C;">
                            <p class="text-sm font-semibold" style="color: #2E7D32;">Unit Cost</p>
                            <p id="unitCost" class="text-xl font-bold mt-1.5" style="color: #2E7D32;">-</p>
                        </div>
                        <div id="productionCostCard" class="p-4 rounded-xl border-l-4 shadow-lg hidden" style="background-color: rgba(255,255,255,0.7); border-left-color: #7C3AED;">
                            <p class="text-sm font-semibold" style="color: #5B21B6;">Production Cost</p>
                            <p id="productionCost" class="text-xl font-bold mt-1.5" style="color: #5B21B6;">-</p>
                        </div>
                    </div>

                    <!-- Materials Needed (Product View) -->
                    <div id="productMaterialsSection" class="mb-5 hidden">
                        <h4 class="text-xl font-bold mb-4 flex items-center" style="color: #374151;">
                            <span class="w-1 h-6 rounded mr-3" style="background-color: #374151;"></span>
                            Materials Needed
                        </h4>
                        <div class="rounded-xl shadow-lg overflow-hidden" style="border: 2px solid #374151; background: rgba(255,255,255,0.8);">
                            <div id="productMaterialsList" class="divide-y max-h-[300px] overflow-y-auto custom-scrollbar" style="border-color: #374151;">
                                <div class="px-6 py-8 text-center font-medium" style="color: #666;">
                                    Loading materials...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Movement History (Materials Only) -->
                    <div id="materialMovementsSection" class="mb-5 hidden">
                        <h4 class="text-xl font-bold mb-4 flex items-center" style="color: #374151;">
                            <span class="w-1 h-6 rounded mr-3" style="background-color: #374151;"></span>
                            Movement History
                        </h4>
                        <div class="overflow-x-auto rounded-xl shadow-lg max-h-[300px] overflow-y-auto custom-scrollbar" style="border: 2px solid #374151; background: rgba(255,255,255,0.8);">
                            <table class="w-full text-sm">
                                <thead style="background-color: #374151;" class="text-white sticky top-0">
                                    <tr>
                                        <th class="px-6 py-4 text-left font-bold">Date</th>
                                        <th class="px-6 py-4 text-left font-bold">Type</th>
                                        <th class="px-6 py-4 text-right font-bold">Quantity</th>
                                        <th class="px-6 py-4 text-left font-bold">Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="materialMovementsBody" class="divide-y" style="border-color: #374151;">
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center font-medium" style="color: #666;">
                                            Loading movements...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="closeStockModal()" class="px-6 py-1.5 rounded-xl hover:shadow-lg transition-all duration-200 text-sm font-bold text-white shadow-lg" style="background-color: #374151;">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Logs Modal -->
        <!-- Stock Logs Modal - REFINED VERSION -->
        <div id="stockLogsModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-600" style="background-color: #FFF1DA;">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-5 border-b-2" style="border-color: #374151;">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-500/10 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold" style="color: #374151;">Stock Logs</h3>
                                <p class="text-gray-600 text-xs mt-0.5">Complete audit trail of all inventory movements</p>
                            </div>
                        </div>
                        <button onclick="closeStockLogsModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-5 overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 85px);">
                        <!-- Quick Filters -->
                        <div class="mb-4 flex flex-wrap gap-2">
                             <button type="button" onclick="setQuickFilter('today')" class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 hover:text-amber-800 rounded-lg transition-all border border-amber-200">
                                Today
                            </button>
                             <button type="button" onclick="setQuickFilter('yesterday')" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-all border border-gray-200">
                                Yesterday
                            </button>
                            <button type="button" onclick="setQuickFilter('last_week')" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-all border border-gray-200">
                                Last Week
                            </button>
                            <button type="button" onclick="setQuickFilter('1_month')" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-all border border-gray-200">
                                1 Month
                            </button>
                            <button type="button" onclick="setQuickFilter('1_year')" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-all border border-gray-200">
                                1 Year
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Search & Filter</label>
                                <div class="relative">
                                    <input type="text" id="logSearchFilter" placeholder="Search by material, supplier..." class="w-full border border-gray-300 rounded-xl pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" oninput="filterStockLogs()">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">From Date</label>
                                <input type="date" id="logDateFromFilter" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" onchange="loadStockLogs()">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">To Date</label>
                                <input type="date" id="logDateToFilter" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" onchange="loadStockLogs()">
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all">
                                <p class="text-green-800 text-xs font-bold uppercase tracking-wider">Total In</p>
                                <p id="logTotalIn" class="text-2xl font-bold text-green-700 mt-1">0</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all">
                                <p class="text-red-800 text-xs font-bold uppercase tracking-wider">Total Out</p>
                                <p id="logTotalOut" class="text-2xl font-bold text-red-700 mt-1">0</p>
                            </div>
                            <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all">
                                <p class="text-slate-800 text-xs font-bold uppercase tracking-wider">Total Logs</p>
                                <p id="logTotalMovements" class="text-2xl font-bold text-slate-700 mt-1">0</p>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="mb-4">
                            <div class="flex p-1 bg-gray-200/50 rounded-xl inline-flex">
                                <button onclick="switchTab('stockIn')" id="tabStockIn" class="tab-button active px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Stock IN
                                </button>
                                <button onclick="switchTab('stockOut')" id="tabStockOut" class="tab-button px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2 text-gray-500 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                    Stock OUT
                                </button>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content-container relative min-h-[300px]">
                            <!-- Stock IN Section -->
                            <div id="contentStockIn" class="tab-content active absolute inset-0 w-full transition-all duration-300">
                                <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-gray-100/80 text-gray-700 font-bold uppercase tracking-wider sticky top-0">
                                                <tr>
                                                    <th class="px-4 py-3">Date & Time</th>
                                                    <th class="px-4 py-3">Material</th>
                                                    <th class="px-4 py-3 text-center">Quantity</th>
                                                    <th class="px-4 py-3">PO ID</th>
                                                    <th class="px-4 py-3">User</th>
                                                    <th class="px-4 py-3">Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stockInTable" class="divide-y divide-gray-100">
                                                <tr>
                                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock IN records</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock OUT Section -->
                            <div id="contentStockOut" class="tab-content hidden absolute inset-0 w-full transition-all duration-300">
                                <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-gray-100/80 text-gray-700 font-bold uppercase tracking-wider sticky top-0">
                                                <tr>
                                                    <th class="px-4 py-3">Date & Time</th>
                                                    <th class="px-4 py-3">Material</th>
                                                    <th class="px-4 py-3 text-center">Quantity Out</th>
                                                    <th class="px-4 py-3">Work Order</th>
                                                    <th class="px-4 py-3">User</th>
                                                    <th class="px-4 py-3">Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stockOutTable" class="divide-y divide-gray-100">
                                                <tr>
                                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock OUT records</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            /* Custom Tab Styling */
                            .tab-button.active {
                                background-color: white;
                                color: #111827; /* gray-900 */
                                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                            }
                            
                            /* Fade Animation */
                            .tab-content {
                                opacity: 1;
                                transform: translateY(0);
                                pointer-events: auto;
                            }
                            
                            .tab-content.hidden {
                                display: block; /* Keep textual display block for transition, but hide visually */
                                opacity: 0;
                                transform: translateY(10px);
                                pointer-events: none;
                                z-index: -1;
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteConfirmationModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl overflow-hidden border-2 border-red-200">
                <!-- Header with Destructive Theme -->
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b-2 border-red-200">
                    <div class="flex items-center space-x-3">
                        <!-- Warning Triangle Icon -->
                        <svg class="w-7 h-7 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <h3 id="deleteItemTitle" class="text-lg font-bold text-red-900">Delete Item?</h3>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-5">
                    <!-- Impact Explanation -->
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm font-medium mb-3">
                            You are about to delete:
                        </p>
                        <div class="bg-red-50 border-l-4 border-red-600 px-4 py-2 rounded">
                            <p id="deleteItemName" class="text-red-900 font-bold text-sm">Item Name</p>
                        </div>
                    </div>

                    <!-- Warning Messages -->
                    <div class="space-y-2 mb-5 text-xs text-gray-600">
                        <div class="flex items-start space-x-2">
                            <span class="text-red-600 font-bold mt-0.5">•</span>
                            <span><strong>This action cannot be undone.</strong></span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <span class="text-red-600 font-bold mt-0.5">•</span>
                            <span>All associated data will be permanently removed from the system.</span>
                        </div>
                    </div>

                    <!-- Confirmation Text Input -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-700 mb-2">
                            Type <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-red-600">DELETE</span> to confirm:
                        </label>
                        <input 
                            type="text" 
                            id="deleteConfirmationInput" 
                            placeholder="Type DELETE" 
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-medium"
                        />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <!-- Cancel Button (Left/Secondary) -->
                    <button 
                        onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 font-bold text-sm hover:bg-gray-100 hover:border-gray-400 transition-all"
                    >
                        Cancel
                    </button>
                    <!-- Delete Button (Right/Primary - Destructive) -->
                    <button 
                        id="deleteConfirmButton"
                        onclick="confirmDelete()" 
                        disabled
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-bold text-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg"
                    >
                        Delete Permanently
                    </button>
                </div>
            </div>
        </div>

        <!-- Generic Confirmation Modal (reused for add/update/remove actions) -->
        <div id="genericConfirmModal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-50 flex">
            <div class="flex items-center justify-center min-h-screen p-3 w-full">
                <div id="genericConfirmContainer" class="bg-amber-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-slate-700">
                    <div id="genericConfirmHeader" class="sticky top-0 bg-gradient-to-r from-red-600 to-red-700 p-3 text-white rounded-t-xl z-10">
                        <div class="flex items-center justify-between">
                            <h3 id="genericConfirmTitle" class="text-lg font-bold">Confirm</h3>
                            <button onclick="closeGenericConfirm()" class="text-white hover:text-slate-200 hover:bg-white/10 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <p id="genericConfirmMessage" class="text-slate-700 mb-6">Are you sure?</p>
                        <div class="flex gap-3">
                            <button onclick="closeGenericConfirm()" class="flex-1 px-4 py-3 border-2 border-slate-400 text-slate-700 bg-white rounded-xl hover:bg-slate-50 transition-all font-bold shadow-sm hover:shadow-md">Cancel</button>
                            <button id="genericConfirmButton" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold shadow-lg hover:shadow-xl">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Initialize search and filter listeners on page load
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const categoryFilter = document.getElementById('categoryFilter');
                
                if (searchInput) {
                    searchInput.addEventListener('keyup', applyInventoryFilters);
                    searchInput.addEventListener('change', applyInventoryFilters);
                }
                
                if (categoryFilter) {
                    categoryFilter.addEventListener('change', applyInventoryFilters);
                }
            });

            // Tab functionality
            function showTab(tab) {
                const materialsTab = document.getElementById('materials-tab');
                const productsTab = document.getElementById('products-tab');
                const materialsTable = document.getElementById('materials-table');
                const productsTable = document.getElementById('products-table');
                const materialsButton = document.getElementById('materialsButton');
                const productsButton = document.getElementById('productsButton');
                
                if (tab === 'materials') {
                    materialsTab.style.backgroundColor = '#FFF1DA';
                    materialsTab.style.color = '#111827';
                    materialsTab.style.borderColor = 'transparent';
                    productsTab.style.backgroundColor = '#475569';
                    productsTab.style.color = '#FFFFFF';
                    productsTab.style.borderColor = '#64748b';
                    materialsTable.classList.remove('hidden');
                    productsTable.classList.add('hidden');
                    materialsButton.classList.remove('hidden');
                    productsButton.classList.add('hidden');
                } else {
                    productsTab.style.backgroundColor = '#FFF1DA';
                    productsTab.style.color = '#111827';
                    productsTab.style.borderColor = 'transparent';
                    materialsTab.style.backgroundColor = '#475569';
                    materialsTab.style.color = '#FFFFFF';
                    materialsTab.style.borderColor = '#64748b';
                    productsTable.classList.remove('hidden');
                    materialsTable.classList.add('hidden');
                    materialsButton.classList.add('hidden');
                    productsButton.classList.remove('hidden');
                }

                // Save active tab to localStorage
                localStorage.setItem('activeInventoryTab', tab);
                applyInventoryFilters();
            }

            // Restore active tab on page load
            window.addEventListener('DOMContentLoaded', function() {
                const activeTab = localStorage.getItem('activeInventoryTab') || 'materials';
                showTab(activeTab);
            });

            function applyInventoryFilters() {
                const searchValue = (document.getElementById('searchInput')?.value || '').toLowerCase();
                const categoryValue = document.getElementById('categoryFilter')?.value || '';

                const materialsTable = document.getElementById('materials-table');
                const productsTable = document.getElementById('products-table');

                const cards = [];
                if (materialsTable && !materialsTable.classList.contains('hidden')) {
                    cards.push(...materialsTable.querySelectorAll('[data-name]'));
                }
                if (productsTable && !productsTable.classList.contains('hidden')) {
                    cards.push(...productsTable.querySelectorAll('[data-name]'));
                }

                cards.forEach(card => {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    const category = card.getAttribute('data-category') || '';

                    const matchesSearch = !searchValue || name.includes(searchValue);
                    const matchesCategory = !categoryValue || category === categoryValue;

                    card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
                });
            }

            // Modal functions
            function openAddItemModal() {
                document.getElementById('addItemModal').classList.remove('hidden');
            }

            function closeAddItemModal() {
                document.getElementById('addItemModal').classList.add('hidden');
                document.getElementById('addItemForm').reset();
                // Clear selection states
                document.querySelectorAll('#materialCategoryButtons .category-btn').forEach(btn => {
                    btn.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    btn.classList.add('border-gray-300', 'text-gray-600');
                });
                document.getElementById('selectedMaterialCategory').value = '';
                document.querySelectorAll('#supplierButtonsContainer .supplier-btn').forEach(btn => {
                    btn.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    btn.classList.add('border-gray-200');
                    btn.querySelector('div div').classList.remove('bg-amber-500');
                    btn.querySelector('div div').classList.add('bg-slate-300');
                });
                document.getElementById('selectedMaterialSupplierId').value = '';
                if(document.getElementById('supplierSearchInput')) document.getElementById('supplierSearchInput').value = '';
                document.querySelectorAll('.supplier-btn').forEach(btn => btn.classList.remove('hidden'));
            }

            function selectMaterialCategory(btn, category) {
                document.querySelectorAll('#materialCategoryButtons .category-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-300', 'text-gray-600');
                });
                btn.classList.remove('border-gray-300', 'text-gray-600');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                document.getElementById('selectedMaterialCategory').value = category;
            }

            function selectSupplier(btn, supplierId) {
                document.querySelectorAll('#supplierButtonsContainer .supplier-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-200');
                    b.querySelector('div div').classList.remove('bg-amber-500');
                    b.querySelector('div div').classList.add('bg-slate-300');
                });
                btn.classList.remove('border-gray-200');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                btn.querySelector('div div').classList.remove('bg-slate-300');
                btn.querySelector('div div').classList.add('bg-amber-500');
                document.getElementById('selectedMaterialSupplierId').value = supplierId;
            }

            function selectProductCategory(btn, category) {
                document.querySelectorAll('#productCategoryButtons .prod-category-btn').forEach(b => {
                    b.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    b.classList.add('border-gray-300', 'text-gray-600');
                });
                btn.classList.remove('border-gray-300', 'text-gray-600');
                btn.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                document.getElementById('selectedProductCategory').value = category;
            }

            // Supplier Search
            document.addEventListener('DOMContentLoaded', function() {
                const supplierSearch = document.getElementById('supplierSearchInput');
                if (supplierSearch) {
                    supplierSearch.addEventListener('input', function() {
                        const term = this.value.toLowerCase();
                        document.querySelectorAll('.supplier-btn').forEach(btn => {
                            const name = btn.dataset.supplierName;
                            btn.classList.toggle('hidden', !name.includes(term));
                        });
                    });
                }
            });

            function openAddProductModal() {
                document.getElementById('addProductModal').classList.remove('hidden');
                document.getElementById('materialsContainer').innerHTML = '';
            }

            function closeAddProductModal() {
                document.getElementById('addProductModal').classList.add('hidden');
                document.getElementById('addProductForm').reset();
                document.getElementById('materialsContainer').innerHTML = '';
            }

            // Material row counter
            let materialRowCount = 0;

            function addMaterialRow() {
                const materialsContainer = document.getElementById('materialsContainer');
                const rowId = 'material-row-' + materialRowCount++;
                
                const row = document.createElement('div');
                row.id = rowId;
                row.className = 'flex gap-2 items-end bg-white/70 p-4 rounded-xl border-2 border-gray-300';
                
                row.innerHTML = `
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Material *</label>
                        <select name="materials[${materialRowCount-1}][material_id]" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                            <option value="">Select a material...</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Quantity Needed *</label>
                        <input type="number" name="materials[${materialRowCount-1}][quantity_needed]" step="0.01" min="0.01" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                    </div>
                    <button type="button" onclick="removeMaterialRow('${rowId}')" class="px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold shadow-lg transition-all">
                        Remove
                    </button>
                `;
                
                materialsContainer.appendChild(row);
            }

            function removeMaterialRow(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    openGenericConfirm('Remove Material', 'Remove this material from the product?', function() {
                        row.remove();
                    });
                }
            }

            // Edit material row counter
            let editMaterialRowCount = 0;

            function openEditProductModal(productId) {
                fetch(`/inventory/${productId}/edit-product`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editProductName').value = data.product_name;
                        document.getElementById('editProductUnit').value = data.unit;
                        document.getElementById('editProductCategory').value = data.category;
                        document.getElementById('editProductProductionCost').value = data.production_cost;
                        document.getElementById('editProductSellingPrice').value = data.selling_price;
                        
                        document.getElementById('editProductForm').action = `/inventory/${productId}`;
                        
                        document.getElementById('editMaterialsContainer').innerHTML = '';
                        editMaterialRowCount = 0;
                        
                        if (data.materials && data.materials.length > 0) {
                            data.materials.forEach(material => {
                                addEditMaterialRow(material.id, material.name, material.unit, material.pivot.quantity_needed);
                            });
                        }
                        
                        document.getElementById('editProductModal').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error loading product:', error);
                        showErrorNotification('Error loading product details');
                    });
            }

            function closeEditProductModal() {
                document.getElementById('editProductModal').classList.add('hidden');
                document.getElementById('editProductForm').reset();
                document.getElementById('editMaterialsContainer').innerHTML = '';
            }

            function addEditMaterialRow(materialId = null, materialName = null, materialUnit = null, quantityNeeded = null) {
                const materialsContainer = document.getElementById('editMaterialsContainer');
                const rowId = 'edit-material-row-' + editMaterialRowCount++;
                
                const row = document.createElement('div');
                row.id = rowId;
                row.className = 'flex gap-2 items-end bg-white/70 p-4 rounded-xl border-2 border-gray-300';
                
                row.innerHTML = `
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Material *</label>
                        <select name="materials[${editMaterialRowCount-1}][material_id]" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                            <option value="">Select a material...</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" ${materialId == {{ $material->id }} ? 'selected' : ''}>{{ $material->name }} ({{ $material->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Quantity Needed *</label>
                        <input type="number" name="materials[${editMaterialRowCount-1}][quantity_needed]" step="0.01" min="0.01" value="${quantityNeeded || ''}" class="w-full border-2 border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" required>
                    </div>
                    <button type="button" onclick="removeEditMaterialRow('${rowId}')" class="px-3 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold shadow-lg transition-all">
                        Remove
                    </button>
                `;
                
                materialsContainer.appendChild(row);
            }

            function removeEditMaterialRow(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    openGenericConfirm('Remove Material', 'Remove this material from the product?', function() {
                        row.remove();
                    });
                }
            }

            document.getElementById('addProductForm')?.addEventListener('submit', function() {
                localStorage.setItem('activeInventoryTab', 'products');
            });

            document.getElementById('editProductForm')?.addEventListener('submit', function() {
                localStorage.setItem('activeInventoryTab', 'products');
            });

            function openStockModal(type, id) {
                document.getElementById('stockModal').classList.remove('hidden');
                
                fetch(`/inventory/${id}/details?type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('itemName').textContent = data.item.name || data.item.product_name || 'Item';
                        const currentStockCard = document.getElementById('currentStockCard');
                        const minimumStockCard = document.getElementById('minimumStockCard');
                        const unitCostCard = document.getElementById('unitCostCard');
                        const productionCostCard = document.getElementById('productionCostCard');
                        const stockModalContent = document.getElementById('stockModalContent');
                        const itemInfoGrid = document.getElementById('itemInfoGrid');

                        if (type === 'product') {
                            stockModalContent.classList.remove('max-w-4xl');
                            stockModalContent.classList.add('max-w-2xl');
                            itemInfoGrid.classList.remove('grid-cols-3');
                            itemInfoGrid.classList.add('grid-cols-2');

                            currentStockCard.classList.add('hidden');
                            minimumStockCard.classList.add('hidden');
                            productionCostCard.classList.remove('hidden');

                            document.getElementById('unitCost').textContent = data.item.selling_price
                                ? '₱' + parseFloat(data.item.selling_price).toFixed(2)
                                : 'N/A';
                            document.getElementById('productionCost').textContent = data.item.production_cost
                                ? '₱' + parseFloat(data.item.production_cost).toFixed(2)
                                : 'N/A';
                        } else {
                            stockModalContent.classList.remove('max-w-2xl');
                            stockModalContent.classList.add('max-w-4xl');
                            itemInfoGrid.classList.remove('grid-cols-2');
                            itemInfoGrid.classList.add('grid-cols-3');

                            currentStockCard.classList.remove('hidden');
                            minimumStockCard.classList.remove('hidden');
                            productionCostCard.classList.add('hidden');

                            document.getElementById('currentStock').textContent = data.item.current_stock + ' ' + (data.item.unit || '');
                            document.getElementById('minimumStock').textContent = data.item.minimum_stock || '-';
                            document.getElementById('unitCost').textContent = data.item.unit_cost
                                ? '₱' + parseFloat(data.item.unit_cost).toFixed(2)
                                : 'N/A';
                        }

                        const materialsSection = document.getElementById('productMaterialsSection');
                        const materialsList = document.getElementById('productMaterialsList');
                        const materialMovementsSection = document.getElementById('materialMovementsSection');
                        const materialMovementsBody = document.getElementById('materialMovementsBody');

                        if (type === 'product') {
                            materialsSection.classList.remove('hidden');
                            materialMovementsSection.classList.add('hidden');
                            materialMovementsBody.innerHTML = '';

                            if (data.materials && data.materials.length > 0) {
                                materialsList.innerHTML = data.materials.map(material => `
                                    <div class="flex items-center justify-between px-6 py-4">
                                        <div class="text-gray-900 font-medium">• ${material.name}</div>
                                        <div class="text-gray-700 font-bold">${material.quantity_needed} ${material.unit}</div>
                                    </div>
                                `).join('');
                            } else {
                                materialsList.innerHTML = '<div class="px-6 py-8 text-center font-medium" style="color: #666;">No materials assigned</div>';
                            }
                        } else {
                            materialsSection.classList.add('hidden');
                            materialsList.innerHTML = '';

                            materialMovementsSection.classList.remove('hidden');
                            if (data.movements && data.movements.length > 0) {
                                materialMovementsBody.innerHTML = data.movements.map(movement => `
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="px-6 py-1.5 text-gray-900 font-medium">${new Date(movement.created_at).toLocaleDateString()}</td>
                                        <td class="px-6 py-1.5">
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-xl ${
                                                movement.movement_type === 'in' ? 'bg-green-100 text-green-800' :
                                                movement.movement_type === 'out' ? 'bg-red-100 text-red-800' :
                                                'bg-blue-100 text-blue-800'
                                            }">
                                                ${movement.movement_type.charAt(0).toUpperCase() + movement.movement_type.slice(1)}
                                            </span>
                                        </td>
                                        <td class="px-6 py-1.5 text-right font-bold text-gray-900">${movement.quantity}</td>
                                        <td class="px-6 py-1.5 text-gray-600">${movement.notes || '-'}</td>
                                    </tr>
                                `).join('');
                            } else {
                                materialMovementsBody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No movements recorded yet</td></tr>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching item details:', error);
                        const materialsSection = document.getElementById('productMaterialsSection');
                        const materialsList = document.getElementById('productMaterialsList');
                        materialsSection.classList.add('hidden');
                        materialsList.innerHTML = '';
                        const materialMovementsSection = document.getElementById('materialMovementsSection');
                        const materialMovementsBody = document.getElementById('materialMovementsBody');
                        materialMovementsSection.classList.add('hidden');
                        materialMovementsBody.innerHTML = '';
                    });
            }

            function closeStockModal() {
                document.getElementById('stockModal').classList.add('hidden');
            }

            let deleteConfirmState = {
                type: null,
                id: null,
                name: null
            };

            function openDeleteModal(type, id, name) {
                deleteConfirmState = { type, id, name };
                document.getElementById('deleteConfirmationModal').classList.remove('hidden');
                document.getElementById('deleteConfirmationInput').value = '';
                document.getElementById('deleteConfirmButton').disabled = true;
                
                // Update modal header with specific item name
                const itemLabel = type === 'material' ? 'Material' : 'Product';
                document.getElementById('deleteItemTitle').textContent = `Delete ${itemLabel}: "${name}"?`;
                document.getElementById('deleteItemName').textContent = name;
            }

            function closeDeleteModal() {
                document.getElementById('deleteConfirmationModal').classList.add('hidden');
                deleteConfirmState = { type: null, id: null, name: null };
                document.getElementById('deleteConfirmationInput').value = '';
                document.getElementById('deleteConfirmButton').disabled = true;
            }

            function confirmDelete() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/inventory/${deleteConfirmState.id}/${deleteConfirmState.type}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                
                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }

            // Update delete button state based on input
            document.addEventListener('DOMContentLoaded', function() {
                const deleteInput = document.getElementById('deleteConfirmationInput');
                const deleteButton = document.getElementById('deleteConfirmButton');
                if (deleteInput) {
                    deleteInput.addEventListener('input', function() {
                        deleteButton.disabled = this.value.trim() !== 'DELETE';
                    });
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('fixed')) {
                    closeAddItemModal();
                    closeAddProductModal();
                    closeStockModal();
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                showTab('materials');
                const searchInput = document.getElementById('searchInput');
                const categoryFilter = document.getElementById('categoryFilter');
                if (searchInput) {
                    searchInput.addEventListener('input', applyInventoryFilters);
                }
                if (categoryFilter) {
                    categoryFilter.addEventListener('change', applyInventoryFilters);
                }
            });

            // Generic confirmation modal handlers (replace confirm() with modal)
            // Modal HTML (will be inserted near script end if not present)
            function openGenericConfirm(title, message, onConfirm, variant = 'danger') {
                // ensure modal exists
                const modal = document.getElementById('genericConfirmModal');
                if (!modal) return onConfirm();

                document.getElementById('genericConfirmTitle').textContent = title || 'Confirm';
                document.getElementById('genericConfirmMessage').textContent = message || '';
                const confirmBtn = document.getElementById('genericConfirmButton');
                const header = document.getElementById('genericConfirmHeader');
                const container = document.getElementById('genericConfirmContainer');

                // clear previous handlers
                confirmBtn.onclick = null;
                confirmBtn.onclick = function() {
                    closeGenericConfirm();
                    if (typeof onConfirm === 'function') onConfirm();
                };

                // set variant classes: 'danger' -> red (default), 'positive' -> green
                const baseClasses = 'flex-1 px-4 py-3 rounded-xl transition-all font-bold shadow-lg hover:shadow-xl';
                if (variant === 'positive') {
                    confirmBtn.className = baseClasses + ' bg-green-600 text-white hover:bg-green-700';
                    if (header) header.className = 'sticky top-0 bg-gradient-to-r from-green-600 to-green-700 p-3 text-white rounded-t-xl z-10';
                    if (container) {
                        container.className = 'bg-green-50 rounded-xl max-w-lg w-full overflow-y-auto shadow-2xl border-2 border-green-700';
                    }
                } else {
                    confirmBtn.className = baseClasses + ' bg-red-600 text-white hover:bg-red-700';
                }

                modal.classList.remove('hidden');
            }

            function closeGenericConfirm() {
                const modal = document.getElementById('genericConfirmModal');
                if (!modal) return;
                modal.classList.add('hidden');
            }

            // Attach form submit interceptors that open the modal
            document.addEventListener('DOMContentLoaded', function() {
                const addForm = document.getElementById('addProductForm');
                if (addForm) {
                    addForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Add Product', 'Are you sure you want to add this product with the listed materials?', function() {
                            addForm.submit();
                        }, 'positive');
                    });
                }

                // Add Item (material) form confirmation
                const addItemForm = document.getElementById('addItemForm');
                if (addItemForm) {
                    addItemForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Add Material', 'Are you sure you want to add this material?', function() {
                            addItemForm.submit();
                        }, 'positive');
                    });
                }

                const editForm = document.getElementById('editProductForm');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openGenericConfirm('Update Product', 'Are you sure you want to update this product and its materials?', function() {
                            editForm.submit();
                        }, 'positive');
                    });
                }
            });

            function openStockLogsModal() {
                document.getElementById('stockLogsModal').classList.remove('hidden');
                loadStockLogs();
            }

            function closeStockLogsModal() {
                document.getElementById('stockLogsModal').classList.add('hidden');
            }

            function loadStockLogs() {
                const dateFrom = document.getElementById('logDateFromFilter')?.value || '';
                const dateTo = document.getElementById('logDateToFilter')?.value || '';

                const params = new URLSearchParams();
                if (dateFrom) params.append('date_from', dateFrom);
                if (dateTo) params.append('date_to', dateTo);

                const stockMovementsUrl = `{{ route('inventory.stock-movements-report') }}?${params.toString()}`;
                fetch(stockMovementsUrl)
                    .then(res => res.json())
                    .then(movementData => {
                        if (movementData.success) {
                            displayStockLogs(movementData.movements, movementData.summary);
                        }
                    })
                    .catch(err => console.error('Error loading logs:', err));
            }

            function filterStockLogs() {
                const searchText = document.getElementById('logSearchFilter')?.value.toLowerCase() || '';
                const stockInBody = document.getElementById('stockInTable');
                const stockOutBody = document.getElementById('stockOutTable');

                if (!searchText) {
                    // If search is empty, show all rows
                    document.querySelectorAll('#stockInTable tr, #stockOutTable tr').forEach(row => {
                        if (row.querySelector('td[colspan]')) return; // Skip empty message rows
                        row.style.display = '';
                    });
                    return;
                }

                // Filter Stock IN table
                const stockInRows = stockInBody.querySelectorAll('tr:not(:has(td[colspan]))');
                stockInRows.forEach(row => {
                    const material = row.cells[1]?.textContent.toLowerCase() || '';
                    const poId = row.cells[3]?.textContent.toLowerCase() || '';
                    const supplier = row.cells[4]?.textContent.toLowerCase() || '';
                    const notes = row.cells[5]?.textContent.toLowerCase() || '';
                    
                    const matches = material.includes(searchText) || poId.includes(searchText) || 
                                  supplier.includes(searchText) || notes.includes(searchText);
                    row.style.display = matches ? '' : 'none';
                });

                // Filter Stock OUT table
                const stockOutRows = stockOutBody.querySelectorAll('tr:not(:has(td[colspan]))');
                stockOutRows.forEach(row => {
                    const material = row.cells[1]?.textContent.toLowerCase() || '';
                    const workOrder = row.cells[3]?.textContent.toLowerCase() || '';
                    const reference = row.cells[4]?.textContent.toLowerCase() || '';
                    const notes = row.cells[5]?.textContent.toLowerCase() || '';
                    
                    const matches = material.includes(searchText) || workOrder.includes(searchText) || 
                                  reference.includes(searchText) || notes.includes(searchText);
                    row.style.display = matches ? '' : 'none';
                });
            }

            function switchTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });
                
                // Remove active class from all tab buttons
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('active');
                });
                
                // Show selected tab content
                const selectedContent = document.getElementById('content' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                    selectedContent.classList.add('active');
                }
                
                // Add active class to selected tab button
                const selectedButton = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
                if (selectedButton) {
                    selectedButton.classList.add('active');
                }
            }
            
            function displayStockLogs(movements, summary) {
                const stockInBody = document.getElementById('stockInTable');
                const stockOutBody = document.getElementById('stockOutTable');
                const stockOutSummaryBody = document.getElementById('stockOutSummaryTable');

                const stockInRecords = movements.filter(movement => movement.movement_type === 'in');
                const stockOut = movements.filter(movement => movement.movement_type === 'out');

                if (stockInRecords.length === 0) {
                    stockInBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock IN records found</td></tr>';
                } else {
                    stockInBody.innerHTML = stockInRecords.map(movement => {
                        const poId = movement.po_number || '-';
                        const userName = movement.user_name || 'System';
                        const quantity = `${Number(movement.quantity).toFixed(2)} ${movement.unit || ''}`.trim();
                        const dateTime = movement.date ? `${movement.date} ${movement.time || ''}`.trim() : '-';

                        return `
                            <tr class="hover:bg-green-50 transition-all duration-200">
                                <td class="px-4 py-3 text-gray-700 text-sm">${dateTime}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${movement.item_name || 'Unknown Material'}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 font-semibold text-green-700 bg-green-100 rounded-full">+${quantity}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-semibold">${poId}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-600 border border-slate-200">
                                            ${userName.substring(0, 2).toUpperCase()}
                                        </div>
                                        ${userName}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="${movement.notes || '-'}">${movement.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                }

                if (stockOut.length === 0) {
                    stockOutBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No Stock OUT records found</td></tr>';
                } else {
                    stockOutBody.innerHTML = stockOut.map(movement => {
                        const quantity = `${Number(movement.quantity).toFixed(2)} ${movement.unit || ''}`;
                        const woId = movement.wo_id || '-';
                        const userName = movement.user_name || 'System';

                        return `
                            <tr class="hover:bg-red-50 transition-all duration-200">
                                <td class="px-4 py-3 text-gray-700 text-sm">${movement.date} ${movement.time}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${movement.item_name}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 font-semibold text-red-700 bg-red-100 rounded-full">-${quantity}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-semibold">${woId}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-600 border border-slate-200">
                                            ${userName.substring(0, 2).toUpperCase()}
                                        </div>
                                        ${userName}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="${movement.notes || '-'}">${movement.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                }

                document.getElementById('logTotalIn').textContent = Number(summary?.total_in || 0).toFixed(2);
                document.getElementById('logTotalOut').textContent = Number(summary?.total_out || 0).toFixed(2);
                document.getElementById('logTotalMovements').textContent = Number(summary?.total_movements || 0);
            }
        </script>

        <!-- Receive Stock Modal - REFINED VERSION -->
        <div id="receiveStockModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-600" style="background-color: #FFF1DA;">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-5 border-b-2" style="border-color: #374151;">
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-500/10 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold" style="color: #374151;">Receive Stock from Supplier</h3>
                                <p class="text-gray-600 text-xs mt-0.5">Record materials received from purchase orders</p>
                            </div>
                        </div>
                        <button onclick="closeReceiveStockModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Form Content -->
                    <div class="p-5 overflow-y-auto custom-scrollbar" style="max-height: calc(90vh - 85px);">
                        <form id="receiveStockForm" method="POST">
                            <!-- Purchase Order Selection (Enhanced) -->
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Select Purchase Order
                                </label>
                                
                                <div class="bg-white/50 border-2 border-gray-200 rounded-2xl p-4 shadow-inner">
                                    <!-- PO Search -->
                                    <div class="relative mb-4">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <input type="text" id="poSearchInput" placeholder="Search PO # or Supplier..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm outline-none transition-all">
                                    </div>

                                    @php
                                        $purchaseOrders = \App\Models\PurchaseOrder::with('supplier')->where('status', '!=', 'received')->get();
                                    @endphp

                                    <!-- PO Cards Grid -->
                                    <div id="poCardsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                        @forelse($purchaseOrders as $order)
                                            <div onclick="selectPurchaseOrder(this, {{ $order->id }})" 
                                                 class="po-card group p-3 bg-white border-2 border-slate-200 rounded-xl cursor-pointer hover:border-amber-500 hover:bg-amber-50 transition-all shadow-sm"
                                                 data-po-num="{{ $order->order_number ?? 'PO-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}"
                                                 data-supplier="{{ $order->supplier->name }}">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Purchase Order</p>
                                                        <h4 class="font-bold text-gray-800 text-sm mt-0.5">{{ $order->order_number ?? 'PO-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h4>
                                                    </div>
                                                    <div class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-bold text-slate-500 group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors">
                                                        PENDING
                                                    </div>
                                                </div>
                                                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center gap-2">
                                                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                                    <p class="text-xs font-semibold text-slate-600 truncate">{{ $order->supplier->name }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-2 py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                                <p class="text-gray-500 text-xs font-medium">No pending purchase orders found</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    
                                    <!-- Hidden PO Input -->
                                    <input type="hidden" name="purchase_order_id" id="selectedPurchaseOrderId" required>
                                </div>
                            </div>
                            
                            <!-- Received Date Selection -->
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Received Date *
                                </label>
                                <input type="date" name="received_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm font-bold" required>
                            </div>
                            
                            <!-- Items to Receive Section -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        Items to Receive
                                    </h4>
                                    <span id="itemsCount" class="px-2 py-1 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg hidden">0 items</span>
                                </div>
                                
                                <div class="bg-white/50 rounded-xl border border-gray-200 overflow-hidden">
                                    <div id="receiveStockItems" class="overflow-y-auto custom-scrollbar" style="max-height: 40vh;">
                                        <div class="flex flex-col items-center justify-center py-12 px-3">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-gray-500 text-sm font-medium">Select a PO to view items</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notes Section -->
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Additional Notes (Optional)
                                </label>
                                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white shadow-sm" placeholder="Add any notes about this delivery..."></textarea>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-200">
                                <button type="button" onclick="closeReceiveStockModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 font-bold text-xs hover:bg-gray-50 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold text-xs rounded-xl hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Receive Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Enhanced Purchase Order Selection Logic
            function selectPurchaseOrder(card, orderId) {
                // Remove selected class from all cards
                document.querySelectorAll('.po-card').forEach(c => {
                    c.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    c.classList.add('border-slate-200', 'bg-white');
                });

                // Add selected class to current card
                card.classList.remove('border-slate-200', 'bg-white');
                card.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');

                // Update hidden input and load items
                document.getElementById('selectedPurchaseOrderId').value = orderId;
                loadPurchaseOrderItems(orderId);
            }

            // PO Search Logic
            document.addEventListener('DOMContentLoaded', function() {
                const poSearchInput = document.getElementById('poSearchInput');
                if (poSearchInput) {
                    poSearchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const cards = document.querySelectorAll('.po-card');
                        
                        cards.forEach(card => {
                            const poNum = card.dataset.poNum.toLowerCase();
                            const supplier = card.dataset.supplier.toLowerCase();
                            
                            if (poNum.includes(searchTerm) || supplier.includes(searchTerm)) {
                                card.classList.remove('hidden');
                            } else {
                                card.classList.add('hidden');
                            }
                        });
                    });
                }
            });

            // Receive Stock Modal Functions
            function openReceiveStockModal() {
                document.getElementById('receiveStockModal').classList.remove('hidden');
                // Reset PO cards selection
                document.querySelectorAll('.po-card').forEach(c => {
                    c.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/20');
                    c.classList.add('border-slate-200', 'bg-white');
                    c.classList.remove('hidden');
                });
                document.getElementById('selectedPurchaseOrderId').value = '';
                if (document.getElementById('poSearchInput')) document.getElementById('poSearchInput').value = '';
            }

            function closeReceiveStockModal() {
                document.getElementById('receiveStockModal').classList.add('hidden');
                document.getElementById('receiveStockForm').reset();
                document.getElementById('selectedPurchaseOrderId').value = '';
                document.getElementById('receiveStockItems').innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 px-3">
                        <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-slate-500 text-sm font-medium">Please select a purchase order to view items</p>
                        <p class="text-slate-400 text-xs mt-1">Materials from the selected PO will appear here</p>
                    </div>
                `;
                const itemsCount = document.getElementById('itemsCount');
                if (itemsCount) itemsCount.classList.add('hidden');
            }

            // Enhanced loadPurchaseOrderItems function with improved UI
            function loadPurchaseOrderItems(orderId) {
                const container = document.getElementById('receiveStockItems');
                const itemsCount = document.getElementById('itemsCount');

                if (!orderId) {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 px-3">
                            <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-1.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-slate-500 text-sm font-medium">Please select a purchase order to view items</p>
                            <p class="text-slate-400 text-xs mt-1">Materials from the selected PO will appear here</p>
                        </div>
                    `;
                    itemsCount.classList.add('hidden');
                    return;
                }

                container.innerHTML = `
                    <div class="flex items-center justify-center py-12">
                        <div class="flex flex-col items-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500 mb-4"></div>
                            <p class="text-slate-600 text-sm font-medium">Loading items...</p>
                        </div>
                    </div>
                `;

                fetch(`/procurement/purchase-orders/${orderId}/items`)
                    .then(response => response.json())  
                    .then(data => {
                        if (!data.success || !data.items || data.items.length === 0) {
                            container.innerHTML = `
                                <div class="flex flex-col items-center justify-center py-12 px-3">
                                    <svg class="w-16 h-16 text-orange-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-slate-600 text-sm font-bold">No Remaining Items</p>
                                    <p class="text-slate-500 text-xs mt-1">All items have been fully received for this purchase order</p>
                                </div>
                            `;
                            itemsCount.classList.add('hidden');
                            return;
                        }

                        const itemsToReceive = data.items.filter(item => Number(item.remaining_quantity || 0) > 0);
                        
                        if (itemsToReceive.length === 0) {
                            container.innerHTML = `
                                <div class="flex flex-col items-center justify-center py-12 px-3">
                                    <svg class="w-16 h-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-slate-600 text-sm font-bold">All Items Received</p>
                                    <p class="text-slate-500 text-xs mt-1">This purchase order has been fully received</p>
                                </div>
                            `;
                            itemsCount.classList.add('hidden');
                            return;
                        }

                        // Update items count
                        itemsCount.textContent = `${itemsToReceive.length} item${itemsToReceive.length !== 1 ? 's' : ''}`;
                        itemsCount.classList.remove('hidden');

                        const itemsHtml = itemsToReceive.map((item, index) => `
                            <div class="p-2 border-b-2 border-slate-200 hover:bg-amber-50/50 transition-all ${index === 0 ? 'border-t-0' : ''}">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <!-- Material Name -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                            </svg>
                                            Material
                                        </label>
                                        <p class="text-gray-900 font-bold text-sm">${item.material_name}</p>
                                        <p class="text-xs text-gray-500 mt-1 font-medium">Unit: ${item.unit ? item.unit : 'N/A'}</p>
                                    </div>
                                    
                                    <!-- Ordered Quantity -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Ordered</label>
                                        <div class="px-3 py-2 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                            <p class="text-blue-700 font-bold text-sm">${Number(item.ordered_quantity).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Already Received -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Received</label>
                                        <div class="px-3 py-2 bg-green-50 border-2 border-green-200 rounded-lg">
                                            <p class="text-green-700 font-bold text-sm">${Number(item.already_received).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Remaining -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Remaining</label>
                                        <div class="px-3 py-2 bg-amber-50 border-2 border-amber-300 rounded-lg">
                                            <p class="text-amber-700 font-bold text-sm">${Number(item.remaining_quantity).toFixed(2)}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Defect Quantity Input -->
                                    <div>
                                        <label class="block text-xs font-bold text-red-700 mb-2 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Defects
                                        </label>
                                        <input
                                            type="number"
                                            name="items[${index}][defect_quantity]"
                                            step="1"
                                            min="0"
                                            max="${Number(item.remaining_quantity).toFixed(2)}"
                                            value="0"
                                            class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                            placeholder="0.00"
                                            onchange="updateNetQuantity(this, ${index}, ${Number(item.remaining_quantity).toFixed(2)})"
                                        >
                                        <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                    </div>
                                </div>
                                
                                <!-- Net Quantity Display -->
                                <div class="mt-2 p-2 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-green-800 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Net Quantity to Add to Stock:
                                        </span>
                                        <span id="netQty${index}" class="text-lg font-bold text-green-700">
                                            ${Number(item.remaining_quantity).toFixed(2)} ${item.unit}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        container.innerHTML = itemsHtml;
                    })
                    .catch(() => {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-12 px-3">
                                <svg class="w-16 h-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-red-600 text-sm font-bold">Error Loading Items</p>
                                <p class="text-slate-500 text-xs mt-1">Failed to load items for this purchase order</p>
                            </div>
                        `;
                        itemsCount.classList.add('hidden');
                    });
            }

            // Calculate net quantity after defects
            function updateNetQuantity(input, index, remaining) {
                const defectQty = parseFloat(input.value) || 0;
                const netQty = Math.max(0, remaining - defectQty);
                const unit = input.closest('.grid').querySelector('p.text-xs').textContent.replace('Unit: ', '');
                
                const netQtyDisplay = document.getElementById(`netQty${index}`);
                if (netQtyDisplay) {
                    netQtyDisplay.textContent = `${netQty.toFixed(2)} ${unit}`;
                    
                    // Change color based on whether there are defects
                    const container = netQtyDisplay.closest('.bg-gradient-to-r');
                    if (defectQty > 0) {
                        container.className = 'mt-2 p-2 bg-gradient-to-r from-yellow-50 to-yellow-100 border-2 border-yellow-300 rounded-lg';
                        netQtyDisplay.className = 'text-lg font-bold text-yellow-700';
                    } else {
                        container.className = 'mt-2 p-2 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg';
                        netQtyDisplay.className = 'text-lg font-bold text-green-700';
                    }
                }
            }

            // Form submission handler
            const receiveStockForm = document.getElementById('receiveStockForm');
            if (receiveStockForm) {
                receiveStockForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const purchaseOrderInput = document.getElementById('selectedPurchaseOrderId');
                    const purchaseOrderId = purchaseOrderInput ? purchaseOrderInput.value : null;

                    if (!purchaseOrderId) {
                        showErrorNotification('Please select a purchase order first.');
                        return;
                    }

                    const formData = new FormData(receiveStockForm);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(`/procurement/purchase-orders/${purchaseOrderId}/receive-stock`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => {
                            // Handle HTTP error responses
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw {
                                        status: response.status,
                                        message: data.message || `Error: ${response.statusText}`
                                    };
                                }).catch(err => {
                                    if (err.message) throw err;
                                    throw {
                                        status: response.status,
                                        message: response.status === 404 ? 'Invalid PO. Purchase order not found.' : `Error: ${response.statusText}`
                                    };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showSuccessNotification(data.message || 'Stock received successfully.');
                                window.location.reload();
                            } else {
                                // Exception 1.1: Invalid PO error display
                                // Exception 2.1: Duplicate receipt error display
                                // Exception 2.2: Database error display
                                showErrorNotification(data.message || 'Failed to receive stock.');
                            }
                        })
                        .catch(error => {
                            // Display exception error messages
                            const errorMessage = error.message || 'An error occurred while receiving stock.';
                            showErrorNotification(errorMessage);
                            console.error('Stock receive error:', error);
                        });
                });
            }
            // Quick Filter Logic
            function setQuickFilter(period) {
                const today = new Date();
                let fromDate = new Date();
                let toDate = new Date();

                switch(period) {
                    case 'today':
                        // fromDate and toDate already initialized to today
                        break;
                    case 'yesterday':
                        fromDate.setDate(today.getDate() - 1);
                        toDate.setDate(today.getDate() - 1);
                        break;
                    case 'last_week':
                        fromDate.setDate(today.getDate() - 7);
                        // toDate is today
                        break;
                    case '1_month':
                        fromDate.setMonth(today.getMonth() - 1);
                        // toDate is today
                        break;
                    case '1_year':
                        fromDate.setFullYear(today.getFullYear() - 1);
                        // toDate is today
                        break;
                }

                // Format dates as YYYY-MM-DD
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                document.getElementById('logDateFromFilter').value = formatDate(fromDate);
                document.getElementById('logDateToFilter').value = formatDate(toDate);
                
                // Trigger reload
                loadStockLogs();
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

</div>
@endsection