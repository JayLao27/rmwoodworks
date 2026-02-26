<!-- Sidebar -->
<div x-data="{ 
    sidebarOpen: window.innerWidth >= 768 ? (localStorage.getItem('sidebarOpen') !== 'false') : false,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        if (window.innerWidth >= 768) {
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    },
    closeSidebar() {
        if (window.innerWidth < 768) {
            this.sidebarOpen = false;
        }
    },
    handleResize() {
        if (window.innerWidth >= 768) {
            this.sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
        } else {
            this.sidebarOpen = false;
        }
    },
    init() {
        window.addEventListener('resize', () => this.handleResize());
    }
}" 
class="relative z-50"
@keydown.window.escape="closeSidebar()"
@toggle-sidebar.window="toggleSidebar()">

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeSidebar()"
         class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 md:hidden"></div>

    <!-- Sidebar Container -->
    <div :class="sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full md:translate-x-0 md:w-20'" 
         class="fixed md:static inset-y-0 left-0 z-50 bg-[#1e293b] text-white flex flex-col transition-all duration-300 ease-in-out shadow-2xl h-screen border-r border-slate-700/50"
         @touchstart.passive="touchStartX = $event.changedTouches[0].screenX"
         @touchend.passive="touchEndX = $event.changedTouches[0].screenX; if(touchStartX - touchEndX > 50) closeSidebar()">
        
        <!-- Abstract Background Pattern (Subtle) -->
        <div class="absolute inset-0 opacity-5 pointer-events-none overflow-hidden">
            <div class="absolute -top-24 -right-24 w-60 h-60 rounded-full bg-orange-500 blur-3xl"></div>
            <div class="absolute top-1/2 -left-20 w-40 h-40 rounded-full bg-blue-500 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-60 h-60 rounded-full bg-red-500 blur-3xl"></div>
        </div>

        <!-- Toggle Button (Floating) -->
        <button @click="toggleSidebar()" 
                class="absolute -right-4 top-8 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-full p-2 shadow-lg shadow-orange-900/20 hover:shadow-orange-500/30 hover:scale-110 transition-all duration-300 z-50 border-2 border-slate-800 hidden md:block">
            <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <!-- Logo Section -->
        <div class="p-4 relative z-10 border-b border-white/5">
            <div class="flex items-center" :class="sidebarOpen ? 'gap-3' : 'justify-center'">
                <div class="relative group cursor-default">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl blur opacity-30 group-hover:opacity-60 transition-opacity duration-300"></div>
                    <div class="relative w-10 h-10 bg-gradient-to-br from-[#2d3342] to-[#1e222e] rounded-xl flex items-center justify-center shadow-inner border border-slate-600/50">
                        <img src="{{ asset('images/logo-white.png') }}" alt="EMS" class="w-6 h-6 object-contain drop-shadow-md">
                    </div>
                </div>
                
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="overflow-hidden whitespace-nowrap">
                    <h1 class="text-lg font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                        RM WOOD
                    </h1>
                    <p class="text-[9px] uppercase tracking-widest text-orange-500 font-semibold leading-none">Inventory System</p>
                </div>
            </div>
        </div>

        <!-- User Profile Card -->
        <div class="px-3 mt-2 relative z-10">
            <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700/30 p-2 transition-all duration-300 group hover:bg-slate-700/50 hover:border-slate-600"
                 :class="sidebarOpen ? 'flex items-center gap-2.5' : 'flex justify-center'">
                
                <div class="w-8 h-8 rounded bg-gradient-to-br from-indigo-500 to-purple-600 p-0.5 shadow-lg shadow-indigo-500/20 shrink-0">
                    <div class="w-full h-full bg-slate-900 rounded-[3px] flex items-center justify-center text-white font-bold text-[10px]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
                
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="overflow-hidden min-w-0">
                    <p class="text-xs font-bold truncate text-white group-hover:text-indigo-300 transition-colors leading-tight">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                        <p class="text-[9px] text-slate-500 truncate">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'User')) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-2 overflow-y-auto overflow-x-hidden relative z-10 space-y-0.5 custom-scrollbar">
            
            <!-- ===== MENU SECTION ===== -->
            <div x-show="sidebarOpen" class="px-3 mb-1 text-[9px] font-bold uppercase tracking-widest text-slate-600">Menu</div>
            <div x-show="!sidebarOpen" class="flex justify-center mb-1">
                <div class="w-6 h-px bg-slate-700"></div>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group relative mb-0.5 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-600/90 to-red-600/90 text-white shadow-lg ring-1 ring-white/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
               :class="sidebarOpen ? 'justify-start gap-3' : 'justify-center'">
                
                @if(request()->routeIs('dashboard'))
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-white rounded-r-sm shadow-[0_0_8px_rgba(255,255,255,0.5)]" x-show="sidebarOpen"></div>
                @endif

                <div class="{{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>

                <span x-show="sidebarOpen" 
                      class="text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-white' : '' }}">
                    Dashboard
                </span>

                <div x-show="!sidebarOpen" 
                     class="absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded-md shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none border border-slate-700 whitespace-nowrap z-50">
                    Dashboard
                </div>
            </a>

            <!-- Audit Trail -->
            @if(auth()->user()->hasAnyRole(['admin']))
            <a href="{{ route('audit-trails') }}" 
               class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group relative mb-0.5 {{ request()->routeIs('audit-trails*') ? 'bg-gradient-to-r from-orange-600/90 to-red-600/90 text-white shadow-lg ring-1 ring-white/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
               :class="sidebarOpen ? 'justify-start gap-3' : 'justify-center'">
                
                @if(request()->routeIs('audit-trails*'))
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-white rounded-r-sm shadow-[0_0_8px_rgba(255,255,255,0.5)]" x-show="sidebarOpen"></div>
                @endif

                <div class="{{ request()->routeIs('audit-trails*') ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>

                <span x-show="sidebarOpen" 
                      class="text-sm font-medium whitespace-nowrap {{ request()->routeIs('audit-trails*') ? 'text-white' : '' }}">
                    Audit Trail
                </span>

                <div x-show="!sidebarOpen" 
                     class="absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded-md shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none border border-slate-700 whitespace-nowrap z-50">
                    Audit Trail
                </div>
            </a>
            @endif

            <!-- ===== SUBSYSTEMS SECTION ===== -->
            <div class="mt-3 mb-1">
                <div x-show="sidebarOpen" class="px-3 text-[9px] font-bold uppercase tracking-widest text-slate-600">Subsystems</div>
                <div x-show="!sidebarOpen" class="flex justify-center">
                    <div class="w-6 h-px bg-slate-700"></div>
                </div>
            </div>

            @php
                $subsystemItems = [
                    [
                        'name' => 'Inventory',
                        'route' => 'inventory',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        'roles' => ['admin', 'inventory_clerk', 'procurement_officer'],
                        'pattern' => 'inventory*'
                    ],
                    [
                        'name' => 'Production',
                        'route' => 'production',
                        'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                        'roles' => ['admin', 'workshop_staff'],
                        'pattern' => 'production*'
                    ],
                    [
                        'name' => 'Sales & Orders',
                        'route' => 'sales-orders.index',
                        'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                        'roles' => ['admin', 'sales_clerk'],
                        'pattern' => 'sales-orders*'
                    ],
                    [
                        'name' => 'Procurement',
                        'route' => 'procurement',
                        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'roles' => ['admin', 'procurement_officer', 'inventory_clerk'],
                        'pattern' => 'procurement*'
                    ],
                    [
                        'name' => 'Accounting',
                        'route' => 'accounting',
                        'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                        'roles' => ['admin', 'accounting_staff'],
                        'pattern' => 'accounting*'
                    ],
                ];
            @endphp
            @foreach($subsystemItems as $item)
                @if(auth()->user()->hasAnyRole($item['roles']))
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group relative mb-0.5 {{ request()->routeIs($item['pattern']) ? 'bg-gradient-to-r from-orange-600/90 to-red-600/90 text-white shadow-lg ring-1 ring-white/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"
                   :class="sidebarOpen ? 'justify-start gap-3' : 'justify-center'">
                    
                    @if(request()->routeIs($item['pattern']))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-6 bg-white rounded-r-sm shadow-[0_0_8px_rgba(255,255,255,0.5)]" x-show="sidebarOpen"></div>
                    @endif

                    <div class="{{ request()->routeIs($item['pattern']) ? 'text-white' : 'text-slate-400 group-hover:text-orange-400' }} transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>

                    <span x-show="sidebarOpen" 
                          class="text-sm font-medium whitespace-nowrap {{ request()->routeIs($item['pattern']) ? 'text-white' : '' }}">
                        {{ $item['name'] }}
                    </span>

                    <div x-show="!sidebarOpen" 
                         class="absolute left-14 bg-slate-800 text-white text-[10px] px-2 py-1 rounded-md shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none border border-slate-700 whitespace-nowrap z-50">
                        {{ $item['name'] }}
                    </div>
                </a>
                @endif
            @endforeach

        </nav>

        <!-- Footer / Logout -->
        <div class="p-3 relative z-10 border-t border-slate-700/50 bg-slate-900/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full relative overflow-hidden group px-3 py-2 bg-slate-800/50 hover:bg-red-900/30 text-slate-400 hover:text-red-400 rounded-lg transition-all duration-300 border border-slate-700 hover:border-red-800/50 flex items-center shadow-sm"
                        :class="sidebarOpen ? 'justify-start gap-2.5' : 'justify-center'">
                    
                    <svg class="w-4 h-4 relative z-10 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    
                    <span x-show="sidebarOpen" 
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0"
                          x-transition:enter-end="opacity-100"
                          class="text-sm font-medium relative z-10">Log Out</span>
                </button>
            </form>
            
            <div x-show="sidebarOpen" class="mt-2 flex justify-between items-center px-1 text-[9px] text-slate-600">
                <span>© 2025</span>
                <span class="w-0.5 h-0.5 rounded-full bg-slate-700"></span>
                <span>RM Woodworks</span>
            </div>
        </div>
    </div>
</div>