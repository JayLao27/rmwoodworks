@extends('layouts.system')

@section('main-content')
<div class="h-full flex flex-col bg-amber-50 overflow-hidden">
    <!-- Header -->
    <header class="bg-amber-50 p-5 relative z-10 border-b border-amber-200">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-base text-gray-600 mt-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Overview & Analytics
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-xs font-semibold uppercase tracking-wider border border-amber-200">
                    {{ date('F d, Y') }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-5 relative">
        <div class="max-w-7xl mx-auto space-y-5">
            
            <!-- KPI Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Active Orders Card -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Active Orders</h3>
                            <p class="text-2xl font-bold mt-2 bg-gradient-to-r from-amber-300 to-amber-100 bg-clip-text text-transparent">{{ $activeOrdersCount }}</p>
                        </div>
                        <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                            @include('components.icons.cart', ['class' => 'w-5 h-5 text-amber-400'])
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs px-2 py-0.5 rounded {{ $newOrdersThisWeek > 0 ? 'bg-green-500/20 text-green-300' : 'bg-slate-600 text-slate-400' }}">
                            {{ $newOrdersThisWeek > 0 ? '+' . $newOrdersThisWeek . ' New' : 'No new orders' }}
                        </span>
                        <span class="text-slate-400 text-xs">this week</span>
                    </div>
                </div>

                <!-- In Production Card -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">In Production</h3>
                            <p class="text-2xl font-bold mt-2 bg-gradient-to-r from-blue-300 to-blue-100 bg-clip-text text-transparent">{{ $inProductionCount }}</p>
                        </div>
                        <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                             @include('components.icons.time', ['class' => 'w-5 h-5 text-blue-400'])
                        </div>
                    </div>
                    
                    <div class="mt-2">
                         @if($overdueWorkOrders > 0)
                            <div class="flex items-center gap-1.5 text-xs text-red-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                {{ $overdueWorkOrders }} Overdue
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-xs text-green-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                On Track
                            </div>
                        @endif
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-600/50 rounded-full h-1 mt-2 overflow-hidden">
                            <div class="bg-blue-400 h-1 rounded-full transition-all duration-1000" style="width: {{ $inProductionCount > 0 ? min(($inProductionCount / ($activeOrdersCount + $inProductionCount)) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Card -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-3 text-white shadow-xl border border-slate-600 hover:shadow-2xl transition-all duration-300">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xs font-medium text-slate-300 font-semibold uppercase tracking-wide">Low Stock Items</h3>
                            <p class="text-2xl font-bold mt-2 {{ $lowStockCount > 0 ? 'text-red-300' : 'bg-gradient-to-r from-green-300 to-green-100 bg-clip-text text-transparent' }}">{{ $lowStockCount }}</p>
                        </div>
                        <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                            @include('components.icons.alert', ['class' => 'w-5 h-5 ' . ($lowStockCount > 0 ? 'text-red-400' : 'text-green-400')])
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs px-2 py-0.5 rounded {{ $lowStockCount > 0 ? 'bg-red-500/20 text-red-300' : 'bg-green-500/20 text-green-300' }}">
                            {{ $lowStockCount > 0 ? 'Action Required' : 'Healthy' }}
                        </span>
                        <span class="text-slate-400 text-xs">inventory status</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Revenue Chart -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-5 shadow-xl border border-slate-600 text-white">
                    <div class="flex justify-between items-center mb-6 border-b border-slate-600 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-white">Revenue & Expenses</h3>
                            <p class="text-xs text-slate-300 mt-1">Financial performance overview</p>
                        </div>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="revenueExpensesChart"></canvas>
                    </div>
                </div>

                <!-- Profit Chart -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl p-5 shadow-xl border border-slate-600 text-white">
                    <div class="flex justify-between items-center mb-6 border-b border-slate-600 pb-4">
                         <div>
                            <h3 class="text-base font-bold text-white">Net Profit Trend</h3>
                            <p class="text-xs text-slate-300 mt-1">Last 6 months analysis</p>
                        </div>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="netProfitChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bottom Grid: Alerts & Reports -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 pb-8">
                <!-- Low Stock List -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl shadow-xl border border-slate-600 text-white overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-600 flex justify-between items-center bg-black/20">
                        <h3 class="font-bold text-white">Inventory Alerts</h3>
                        @if($lowStockCount > 0)
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        @endif
                    </div>
                    <div class="p-0 overflow-y-auto max-h-[340px] custom-scrollbar flex-1">
                        @if($lowStockMaterials->isEmpty())
                            <div class="flex flex-col items-center justify-center h-48 text-slate-400">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p>All stock levels are optimal</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-600">
                                @foreach($lowStockMaterials as $m)
                                    <div class="flex items-center justify-between p-4 hover:bg-white/5 transition-colors group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 font-bold text-sm border border-amber-500/20">
                                                {{ substr($m->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-slate-200 group-hover:text-amber-400 transition-colors">{{ $m->name }}</h4>
                                                <p class="text-xs text-slate-400">Min: {{ $m->minimum_stock }} {{ $m->unit }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-red-400 font-bold">{{ $m->current_stock }}</p>
                                            <p class="text-xs text-slate-500">{{ $m->unit }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                     <div class="p-3 bg-black/20 border-t border-slate-600">
                        <a href="{{ route('inventory') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors">
                            Manage Inventory
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Financial Report -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl shadow-xl border border-slate-600 text-white overflow-hidden flex flex-col">
                    <div class="p-4 border-b border-slate-600 bg-black/20">
                         <h3 class="font-bold text-white">Monthly Financials</h3>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-800 text-slate-400 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Month</th>
                                    <th class="px-6 py-3 text-right">Revenue</th>
                                    <th class="px-6 py-3 text-right">Expenses</th>
                                    <th class="px-6 py-3 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-600">
                                @foreach ($salesReportMonths as $i => $month)
                                    @php
                                        $rev = $salesReportRevenue[$i] ?? 0;
                                        $exp = $salesReportExpenses[$i] ?? 0;
                                        $profit = $rev - $exp;
                                    @endphp
                                    <tr class="group hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium text-slate-200">{{ $month }}</td>
                                        <td class="px-6 py-4 text-right text-slate-300">₱{{ number_format($rev, 2) }}</td>
                                        <td class="px-6 py-4 text-right text-slate-300">₱{{ number_format($exp, 2) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $profit >= 0 ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                                {{ $profit >= 0 ? '+' : '' }}₱{{ number_format($profit, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-black/20 border-t border-slate-600">
                        <a href="{{ route('accounting') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-amber-400 hover:text-amber-300 transition-colors">
                            View Full Report
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    window.__dashboardData = {
        labels: @json($chartLabels),
        revenue: @json($chartRevenue),
        expenses: @json($chartExpenses),
        profit: @json($chartProfit)
    };
</script>
<script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
@endsection