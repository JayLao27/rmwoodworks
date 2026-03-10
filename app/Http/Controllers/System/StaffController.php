<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

use App\Models\System\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StaffController extends Controller
{
    /**
     * Available staff roles (excludes admin to prevent privilege escalation)
     */
    private const STAFF_ROLES = [
        'inventory_clerk'     => 'Inventory Clerk',
        'procurement_officer' => 'Procurement Officer',
        'workshop_staff'      => 'Workshop Staff',
        'sales_clerk'         => 'Sales Clerk',
        'accounting_staff'    => 'Accounting Staff',
    ];

    /** Cache key for the staff list */
    private const CACHE_KEY  = 'staff_list';
    /** Cache TTL: 10 minutes */
    private const CACHE_TTL  = 600;

    public function index()
    {
        $staffs = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return User::whereIn('role', array_keys(self::STAFF_ROLES))
                ->orderByRaw("FIELD(role, 'inventory_clerk','procurement_officer','workshop_staff','sales_clerk','accounting_staff')")
                ->orderBy('name')
                ->get();
        });

        $roles = self::STAFF_ROLES;

        return view('Systems.staff-management', compact('staffs', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'role'     => 'required|in:' . implode(',', array_keys(self::STAFF_ROLES)),
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Cache::forget(self::CACHE_KEY);

        return redirect()->route('staff.index', [], 303)->with('success', 'Staff account created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $staff = User::findOrFail($id);

        // Prevent editing admin accounts
        if ($staff->role === 'admin') {
            abort(403, 'Cannot modify admin accounts.');
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role'  => 'required|in:' . implode(',', array_keys(self::STAFF_ROLES)),
        ]);

        $staff->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        Cache::forget(self::CACHE_KEY);

        return redirect()->route('staff.index', [], 303)->with('success', 'Staff account updated successfully.');
    }

    public function resetPassword(Request $request, int $id)
    {
        $staff = User::findOrFail($id);

        if ($staff->role === 'admin') {
            abort(403, 'Cannot modify admin accounts.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $staff->update([
            'password' => Hash::make($request->password),
        ]);

        Cache::forget(self::CACHE_KEY);

        return redirect()->route('staff.index', [], 303)->with('success', 'Password reset successfully.');
    }

    public function destroy(int $id)
    {
        $staff = User::findOrFail($id);

        if ($staff->role === 'admin') {
            abort(403, 'Cannot delete admin accounts.');
        }

        $staff->delete();

        Cache::forget(self::CACHE_KEY);

        return redirect()->route('staff.index', [], 303)->with('success', 'Staff account deleted successfully.');
    }

    public function changeAdminPassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'password'                  => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $admin = $request->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('open_admin_modal', true);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('staff.index', [], 303)->with('success', 'Admin password updated successfully.');
    }
}
