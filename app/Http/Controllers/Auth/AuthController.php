<?php
	

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use Illuminate\Validation\Rules;
class AuthController extends Controller
{
	public function showLogin()
	{
		if (Auth::check()) {
			return redirect($this->getHomeRoute());
		}
		return view('auth::Login');
	}

	public function login(Request $request)
	{
		// Step 1 — Input validation (422)
		try {
			$rules = [
				'email' => 'required|email',
				'password' => 'required|string',
			];

			if (config('services.turnstile.enabled')) {
				$rules['cf-turnstile-response'] = ['required', new Turnstile];
			}

			$request->validate($rules, [
				'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
			], [
				'cf-turnstile-response' => 'CAPTCHA',
			]);
		} catch (\Illuminate\Http\Client\RequestException $e) {
			return back()->withErrors(['turnstile' => 'Unable to verify captcha. Please try again.'])->withInput();
		} catch (\Illuminate\Validation\ValidationException $e) {
			return back()->withErrors($e->errors())->withInput();
		}

		// Step 2 — Attempt authentication; isolate credential failures (401) from server errors (500)
		try {
			$authenticated = Auth::attempt(
				$request->only('email', 'password'),
				$request->boolean('remember')
			);
		} catch (\Exception $e) {
			\Log::error('Authentication server error', ['exception' => $e->getMessage()]);
			return back(500)
				->withErrors(['server' => 'A server error occurred while processing your login. Please try again later.'])
				->withInput()
				->with('error_type', 'server');
		}

		if ($authenticated) {
			$request->session()->regenerate();
			return redirect()->intended($this->getHomeRoute());
		}

		// Step 3 — Invalid credentials (401): dedicated key so the view can render a distinct message
		return back()
			->withErrors(['credentials' => 'The email or password you entered is incorrect. Please try again.'])
			->onlyInput('email')
			->with('error_type', 'credentials');
	}

	/**
	 * Get the home route based on user role
	 */
	private function getHomeRoute(): string
	{
		$user = Auth::user();
		
		return match($user->role) {
			'admin' => route('dashboard'),
			'inventory_clerk' => route('inventory'),
			'procurement_officer' => route('procurement'),
			'workshop_staff' => route('production'),
			'sales_clerk' => route('sales'),
			'accounting_staff' => route('dashboard'),
			default => route('dashboard'),
		};
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect()->route('login');
	}
}
