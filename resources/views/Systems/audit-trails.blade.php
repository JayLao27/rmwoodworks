@extends('layouts.system')

@section('main-content')
<div class="flex-1 flex flex-col overflow-hidden bg-amber-50/50">
    <!-- Header Section -->
    <div class="p-8 bg-amber-50 border-b border-amber-200 relative z-10 shadow-sm">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">System Audit Trails</h1>
                    <p class="text-sm font-medium text-gray-600 mt-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Unified chronological view of all system activities and user actions
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="window.location.reload()" class="p-2.5 bg-white border border-amber-200 text-gray-600 rounded-xl hover:bg-amber-50 transition-all shadow-sm group">
                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Role & Date Filters -->
            <div class="flex flex-col gap-6">
                <!-- Row 1: Quick Filters & Search -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="setAuditQuickFilter('today')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ request('date_from') == date('Y-m-d') && request('date_to') == date('Y-m-d') ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            Today
                        </button>
                        <button type="button" onclick="setAuditQuickFilter('yesterday')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ request('date_from') == date('Y-m-d', strtotime('-1 day')) && request('date_to') == date('Y-m-d', strtotime('-1 day')) ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            Yesterday
                        </button>
                        <button type="button" onclick="setAuditQuickFilter('week')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ request('date_from') == date('Y-m-d', strtotime('-7 days')) ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            1 Week
                        </button>
                        <button type="button" onclick="setAuditQuickFilter('month')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ request('date_from') == date('Y-m-d', strtotime('-1 month')) ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            1 Month
                        </button>
                        <button type="button" onclick="setAuditQuickFilter('year')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ request('date_from') == date('Y-m-d', strtotime('-1 year')) ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            1 Year
                        </button>
                    </div>

                    <form id="auditFilterForm" action="{{ route('audit-trails') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        @if($selectedRole)
                            <input type="hidden" name="role" value="{{ $selectedRole }}">
                        @endif
                        <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-amber-200 shadow-sm">
                            <input type="date" name="date_from" id="auditDateFrom" value="{{ $dateFrom }}" class="text-xs font-bold text-gray-700 border-none focus:ring-0 bg-transparent py-1">
                            <span class="text-gray-400 text-xs font-bold">to</span>
                            <input type="date" name="date_to" id="auditDateTo" value="{{ $dateTo }}" class="text-xs font-bold text-gray-700 border-none focus:ring-0 bg-transparent py-1">
                            <button type="submit" class="p-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Row 2: Role Filters -->
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-amber-200/50">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter by Role:</span>
                    <a href="{{ route('audit-trails', request()->only(['date_from', 'date_to'])) }}" 
                       class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ !$selectedRole ? 'bg-slate-800 text-white border-slate-800 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                        All Activity
                    </a>
                    @foreach($availableRoles as $role)
                        <a href="{{ route('audit-trails', array_merge(request()->only(['date_from', 'date_to']), ['role' => $role])) }}" 
                           class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ $selectedRole === $role ? 'bg-slate-800 text-white border-slate-800 shadow-md' : 'bg-white text-gray-600 border-amber-200 hover:bg-amber-50' }}">
                            {{ ucwords(str_replace(['_', '-'], ' ', $role)) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <script>
                function setAuditQuickFilter(period) {
                    const today = new Date();
                    let fromDate = new Date();
                    let toDate = new Date();

                    switch(period) {
                        case 'today':
                            break;
                        case 'yesterday':
                            fromDate.setDate(today.getDate() - 1);
                            toDate.setDate(today.getDate() - 1);
                            break;
                        case 'week':
                            fromDate.setDate(today.getDate() - 7);
                            break;
                        case 'month':
                            fromDate.setMonth(today.getMonth() - 1);
                            break;
                        case 'year':
                            fromDate.setFullYear(today.getFullYear() - 1);
                            break;
                    }

                    const formatDate = (date) => {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    document.getElementById('auditDateFrom').value = formatDate(fromDate);
                    document.getElementById('auditDateTo').value = formatDate(toDate);
                    document.getElementById('auditFilterForm').submit();
                }
            </script>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 p-8 overflow-y-auto custom-scrollbar">
        <div class="max-w-7xl mx-auto">
            
            <!-- Activity Timeline -->
            <div class="relative">
                <!-- Vertical Line -->
                <div class="absolute left-[31px] top-0 bottom-0 w-0.5 bg-amber-200"></div>

                <div class="space-y-8">
                    @forelse($activities as $activity)
                    <div class="relative flex items-start gap-8 group">
                        <!-- Icon Circle -->
                        <div class="relative flex-shrink-0 z-10">
                            <!-- User requested white icons on white/slate bg -->
                            <div class="w-16 h-16 rounded-2xl bg-[#1e293b] border-2 border-slate-700 shadow-lg flex items-center justify-center group-hover:scale-110 transition-all duration-300">
                                @php
                                    $iconClass = "w-7 h-7 text-white";
                                @endphp
                                @switch($activity['type'])
                                    @case('Inventory')
                                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        @break
                                    @case('Sales')
                                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        @break
                                    @case('Accounting')
                                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        @break
                                    @case('Production')
                                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                        @break
                                    @case('Procurement')
                                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <!-- Content Card - User requested #1e293b background with white text -->
                        <div class="flex-1 bg-[#1e293b] rounded-2xl p-6 border border-slate-700 shadow-xl group-hover:shadow-2xl group-hover:border-slate-600 transition-all duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-white/10 text-{{ $activity['color'] }}-400 border border-white/10 shadow-sm">
                                            {{ $activity['type'] }}
                                        </span>
                                        <h3 class="text-base font-bold text-white">{{ $activity['action'] }}</h3>
                                    </div>
                                    <p class="text-sm text-slate-300 leading-relaxed font-medium">{{ $activity['description'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-white">{{ $activity['date']->format('M d, Y') }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 mt-1 uppercase tracking-widest">{{ $activity['date']->format('h:i A') }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-white/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-[10px] font-bold text-white uppercase border border-white/10 shadow-sm">
                                        {{ strtoupper(substr($activity['user'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">{{ $activity['user'] }}</p>
                                        <p class="text-[10px] font-medium text-slate-400">Performed by {{ $activity['user'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $activity['status'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl p-12 border border-amber-200 text-center shadow-sm">
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">No activities recorded yet</h3>
                        <p class="text-sm text-gray-500 max-w-xs mx-auto">Systems activities will appear here in chronological order once you start using the modules.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Load More (Mockup) -->
            <div class="mt-12 text-center pb-8 border-t border-amber-200 pt-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">You've reached the end of recent history</p>
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="px-8 py-3 bg-white border border-amber-200 text-gray-600 text-sm font-bold rounded-2xl shadow-sm hover:bg-amber-50 transition-all flex items-center gap-3 mx-auto">
                    Scroll to Top
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7M5 19l7-7 7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fde68a; /* amber-200 */
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #fcd34d; /* amber-300 */
    }

    /* dynamic color generation for labels */
    .text-emerald-400 { color: #34d399; }
    .text-orange-400 { color: #fbbf24; }
    .text-indigo-400 { color: #818cf8; }
    .text-purple-400 { color: #c084fc; }
    .text-blue-400 { color: #60a5fa; }
    .text-red-400 { color: #f87171; }
</style>
@endsection
