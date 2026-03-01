<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
	public function index() {
		$customers = Customer::all();
		return view('customers.index', compact('customers'));
	}

	public function store(Request $request) {
		$request->validate([
			'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s\'-]+$/',
			'customer_type' => 'required|in:Retail,Contractor,Wholesale',
			'phone' => ['nullable','regex:/^[0-9]{0,12}$/','max:12'],
			'email' => 'nullable|email|max:255|unique:customers,email',
			'address' => 'nullable|string|max:255',
		], [
			'name.required' => 'Customer name is required. Please enter a name.',
			'name.min' => 'Customer name must be at least 2 characters.',
			'name.regex' => 'Customer name can only contain letters, spaces, hyphens, and apostrophes.',
			'customer_type.required' => 'Customer type is required. Please select a type.',
			'phone.regex' => 'Phone must contain only numbers and be up to 12 digits.',
			'email.email' => 'Please enter a valid email address.',
			'email.unique' => 'This email is already registered.',
		]);

		if (empty($request->phone) && empty($request->email)) {
			return redirect()->back()->withInput()->withErrors(['contact' => 'Please provide at least a phone number or email address.']);
		}

		Customer::create($request->only('name','customer_type','phone','email','address'));
		return redirect()->route('sales-orders.index', ['tab' => 'customers'])->with('success', 'Customer added.');
	}

	public function update(Request $request, Customer $customer) {
		$request->validate([
			'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s\'-]+$/',
			'customer_type' => 'required|in:Retail,Contractor,Wholesale',
			'phone' => ['nullable','regex:/^[0-9]{0,12}$/','max:12'],
			'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
			'address' => 'nullable|string|max:255',
		], [
			'name.required' => 'Customer name is required. Please enter a name.',
			'name.min' => 'Customer name must be at least 2 characters.',
			'name.regex' => 'Customer name can only contain letters, spaces, hyphens, and apostrophes.',
			'customer_type.required' => 'Customer type is required. Please select a type.',
			'phone.regex' => 'Phone must contain only numbers and be up to 12 digits.',
			'email.email' => 'Please enter a valid email address.',
			'email.unique' => 'This email is already registered.',
		]);

		if (empty($request->phone) && empty($request->email)) {
			return redirect()->back()->withInput()->withErrors(['contact' => 'Please provide at least a phone number or email address.']);
		}

		$customer->update($request->only('name','customer_type','phone','email','address'));
		return redirect()->route('sales-orders.index', ['tab' => 'customers'])->with('success', 'Customer updated.');
	}

	public function delete(Customer $customer) {
		$customer->delete();
		return redirect()->route('sales-orders.index')->with('success', 'Customer deleted.');
	}

	public function destroy(Customer $customer) {
		$customer->delete();
		return redirect()->route('sales-orders.index', ['tab' => 'customers'])->with('success', 'Customer deleted.');
	}

	public function exportCustomers()
	{
		$customers = Customer::orderBy('name')->get();

		$filename = 'customers-' . now()->format('Y-m-d') . '.csv';
		
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => "attachment; filename=\"$filename\"",
		];

		$callback = function() use ($customers) {
			$file = fopen('php://output', 'w');
			
			// CSV Headers
			fputcsv($file, [
				'Customer Name',
				'Customer Type',
				'Phone',
				'Email',
				'Address',
			]);

			// CSV Data
			foreach ($customers as $customer) {
				fputcsv($file, [
					$customer->name,
					$customer->customer_type,
					$customer->phone ?? 'N/A',
					$customer->email ?? 'N/A',
					$customer->address ?? 'N/A',
				]);
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}
}
