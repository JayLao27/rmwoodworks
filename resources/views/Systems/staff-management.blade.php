@extends('layouts.system')

@section('main-content')
<div class="flex-1 flex flex-col overflow-hidden bg-amber-50">
    <!-- Header Section -->
    <div class="p-8 bg-amber-50 border-b border-amber-200 relative z-10 shadow-sm">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Staff Management</h1>
                    <p class="text-sm font-medium text-gray-600 mt-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        Manage system staff accounts and role assignments
                    </p>
                </div>
                <button onclick="openCreateModal()"
                    class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-slate-800 to-slate-700 text-white rounded-xl hover:from-slate-700 hover:to-slate-600 transition-all shadow-md text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Staff
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto w-full px-8 pt-4">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="flex flex-col gap-1 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium shadow-sm">
                @foreach($errors->all() as $error)
                    <span>• {{ $error }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($roles as $roleKey => $roleLabel)
                    @php $count = $staffs->where('role', $roleKey)->count(); @endphp
                    <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-xl border border-slate-600 p-4 shadow-xl text-center">
                        <div class="text-2xl font-extrabold text-white">{{ $count }}</div>
                        <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mt-1 leading-tight">{{ $roleLabel }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Admin Account Card -->
            <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl border border-slate-600 shadow-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center text-white font-extrabold text-sm shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-extrabold text-white text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-300 mt-0.5">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white">Administrator</span>
                    </div>
                </div>
                <button onclick="openAdminPasswordModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-white/10 text-white hover:bg-white/20 rounded-xl text-xs font-bold transition-all backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change My Password
                </button>
            </div>

            <!-- Staff Table -->
            <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl border border-slate-600 shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-600 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white uppercase tracking-widest">All Staff Accounts</h2>
                    <span class="text-xs text-slate-300 font-medium">{{ $staffs->count() }} total</span>
                </div>

                @if($staffs->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <svg class="w-14 h-14 text-slate-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-slate-300 font-medium text-sm">No staff accounts yet.</p>
                        <p class="text-slate-400 text-xs mt-1">Click "Add Staff" to create the first account.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-900/40">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-slate-300 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-slate-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-slate-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-slate-300 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-slate-300 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-[11px] font-bold text-slate-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-600">
                                @foreach($staffs as $index => $staff)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4 text-slate-400 font-medium text-xs">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                {{ strtoupper(substr($staff->name, 0, 2)) }}
                                            </div>
                                            <span class="font-semibold text-white">{{ $staff->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300">{{ $staff->email }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $roleColors = [
                                                'inventory_clerk'     => 'bg-blue-100 text-blue-700',
                                                'procurement_officer' => 'bg-purple-100 text-purple-700',
                                                'workshop_staff'      => 'bg-yellow-100 text-yellow-700',
                                                'sales_clerk'         => 'bg-green-100 text-green-700',
                                                'accounting_staff'    => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $roleColors[$staff->role] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucwords(str_replace('_', ' ', $staff->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $staff->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditModal({{ $staff->id }}, '{{ addslashes($staff->name) }}', '{{ $staff->email }}', '{{ $staff->role }}')"
                                                class="p-2 bg-white/10 text-slate-300 rounded-lg hover:bg-white/20 transition-all" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button onclick="openResetModal({{ $staff->id }}, '{{ addslashes($staff->name) }}')"
                                                class="p-2 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 transition-all" title="Reset Password">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                            </button>
                                            <button onclick="openDeleteModal({{ $staff->id }}, '{{ addslashes($staff->name) }}')"
                                                class="p-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-all" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ===== CREATE STAFF MODAL ===== -->
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCreateModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" onclick.stop>
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-800">Add New Staff</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Create a new staff account</p>
                </div>
                <button onclick="closeCreateModal()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('staff.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Full Name</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="e.g. Juan Dela Cruz">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="staff@example.com">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Role</label>
                    <input type="hidden" name="role" id="createRole">
                    <div class="flex flex-wrap gap-2">
                        @foreach($roles as $key => $label)
                            <button type="button"
                                id="createRole_{{ $key }}"
                                onclick="selectRole('{{ $key }}', 'create')"
                                class="px-3 py-2 rounded-xl text-xs font-bold border border-gray-200 text-gray-600 hover:bg-slate-100 transition-all">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Re-enter password">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeCreateModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-gradient-to-r from-slate-800 to-slate-700 text-white rounded-xl text-sm font-bold hover:from-slate-700 hover:to-slate-600 transition shadow-md">
                        Create Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== EDIT STAFF MODAL ===== -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-800">Edit Staff</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Update account info and role</p>
                </div>
                <button onclick="closeEditModal()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Full Name</label>
                    <input type="text" name="name" id="editName" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="editEmail" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Role</label>
                    <input type="hidden" name="role" id="editRole">
                    <div class="flex flex-wrap gap-2">
                        @foreach($roles as $key => $label)
                            <button type="button"
                                id="editRole_{{ $key }}"
                                onclick="selectRole('{{ $key }}', 'edit')"
                                class="px-3 py-2 rounded-xl text-xs font-bold border border-gray-200 text-gray-600 hover:bg-slate-100 transition-all">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-gradient-to-r from-slate-800 to-slate-700 text-white rounded-xl text-sm font-bold hover:from-slate-700 hover:to-slate-600 transition shadow-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== RESET PASSWORD MODAL ===== -->
<div id="resetModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeResetModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-800">Reset Password</h3>
                    <p id="resetStaffName" class="text-xs text-gray-500 mt-0.5"></p>
                </div>
                <button onclick="closeResetModal()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="resetForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Re-enter new password">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeResetModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold transition shadow-md">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== DELETE CONFIRM MODAL ===== -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold text-gray-800 mb-1">Delete Staff Account</h3>
            <p id="deleteStaffName" class="text-sm text-gray-500 mb-5"></p>
            <p class="text-xs text-red-500 font-medium mb-5">This action cannot be undone. The account will be permanently removed.</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition shadow-md">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== ADMIN CHANGE PASSWORD MODAL ===== -->
<div id="adminPasswordModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAdminPasswordModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-800">Change Admin Password</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Update your administrator account password</p>
                </div>
                <button onclick="closeAdminPasswordModal()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($errors->has('current_password'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs font-medium">
                    {{ $errors->first('current_password') }}
                </div>
            @endif

            <form method="POST" action="{{ route('staff.admin.change-password') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Enter current password">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">New Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Minimum 8 characters">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent outline-none transition"
                        placeholder="Re-enter new password">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeAdminPasswordModal()"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl text-sm font-bold transition shadow-md">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/staff-management.js') }}?v={{ filemtime(public_path('js/staff-management.js')) }}"></script>
<script>
    // Auto-open modal on validation error
    @if($errors->any() && session('_modal') === 'create')
    openCreateModal();
    @endif
    @if($errors->any() && session('open_admin_modal'))
    openAdminPasswordModal();
    @endif
</script>
@endsection
