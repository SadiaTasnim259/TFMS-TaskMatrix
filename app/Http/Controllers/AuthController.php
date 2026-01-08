<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\AuditLog;

class AuthController extends Controller
{
    /**
     * Display the login form
     * 
     * GET /login
     * Returns: login.blade.php view
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login form submission
     * 
     * POST /login
     * 
     * Logic:
     * 1. Validate email and password are provided
     * 2. Check if user exists
     * 3. Check if account is locked
     * 4. Check if account is active
     * 5. Verify password is correct
     * 6. Lock account if 3 failed attempts
     * 7. Log in user and redirect
     */
    public function login(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = User::where('email', $validated['email'])->first();

        // ❌ User doesn't exist
        if (!$user) {
            return back()->withErrors(['email' => 'Invalid username or password.'])
                ->onlyInput('email');
        }

        // ❌ Account is locked
        if ($user->isLocked()) {
            Log::info("Login attempt for locked user: {$user->email}");
            return back()->withErrors(['email' => 'Your account is locked. Please contact the Administrator.'])
                ->onlyInput('email');
        }

        // ❌ User is inactive
        if (!$user->is_active) {
            Log::info("Login attempt for inactive user: {$user->email}");
            return back()->withErrors(['email' => 'Account is inactive.'])
                ->onlyInput('email');
        }

        // ❌ Password is wrong
        if (!Hash::check($validated['password'], $user->password)) {
            // Increment failed attempts
            $user->incrementFailedAttempts();
            $user->refresh();

            // Check if account is now locked
            if ($user->isLocked()) {
                Log::info("Account locked: {$user->email} (3 failed attempts)");
                return back()->withErrors([
                    'email' => 'Your account is locked. Please contact the Administrator.',
                ])->onlyInput('email');
            }

            Log::info("Failed login: {$user->email} ({$user->failed_login_attempts} attempts)");
            // Generic error message to match "User not found" case
            return back()->withErrors(['email' => 'Invalid username or password.'])
                ->onlyInput('email');
        }

        // ✅ Everything checks out - login successful
        $user->resetLoginAttempts();
        $user->updateLastLogin();

        Auth::login($user);

        // Log login action
        AuditLog::log(
            'login',
            'User',
            $user->id,
            [],
            [],
            "User logged in: {$user->email}",
            $user->name
        );

        if ($user->is_first_login) {
            return redirect('/change-password')->with('first_login', true);
        }

        return redirect('/dashboard')->with('success', 'Logged in successfully');

    }



    /**
     * Handle logout
     * 
     * POST /logout
     * 
     * Logic:
     * 1. Update last_login_at timestamp
     * 2. Clear session
     * 3. Regenerate session token (security)
     * 4. Redirect to login
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::log(
                'logout',
                'User',
                $user->id,
                [],
                [],
                "User logged out: {$user->email}",
                $user->name
            );
        }

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Logged out successfully');
    }

    /**
     * Display forgot password form
     * 
     * GET /forgot-password
     * Returns: forgot-password.blade.php view
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     * 
     * POST /forgot-password
     * 
     * Logic:
     * 1. Validate email exists
     * 2. Generate random token
     * 3. Store token in cache with 1-hour expiry
     * 4. Show success message
     * 
     * Note: In production, send email. Here we log it.
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Find user
        $user = User::where('email', $validated['email'])->first();

        // Generate unique token
        $token = Str::random(32);

        // Store token in cache (expires in 1 hour)
        cache()->put("password_reset_{$user->id}", $token, now()->addHour());

        // Send email
        Mail::to($user->email)->send(new ResetPasswordMail($token));
        Log::info("Password reset email sent to: {$user->email}");

        return back()
            ->with('status', 'A password reset link has been sent to your registered email.');
    }

    /**
     * Display password reset form
     * 
     * GET /reset-password/{token}
     * Returns: reset-password.blade.php view
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset
     * 
     * POST /reset-password
     * 
     * Logic:
     * 1. Validate email, token, new password
     * 2. Check token is valid and not expired
     * 3. Update password
     * 4. Clear is_first_login flag
     * 5. Delete token from cache
     * 6. Redirect to login
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
            'token' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,16}$/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter and one number. Special characters (@$!%*?&) are allowed.',
        ]);

        // Find user
        $user = User::where('email', $validated['email'])->first();

        // Check token is valid
        $storedToken = cache()->get("password_reset_{$user->id}");
        if ($storedToken !== $validated['token']) {
            return back()->withErrors(['token' => 'Invalid or expired token']);
        }

        // Update password and clear flags
        $user->update([
            'password' => $validated['password'], // Hashed automatically
            'is_first_login' => false,
            'must_change_password' => false,
        ]);

        // Delete token
        cache()->forget("password_reset_{$user->id}");

        // Log audit
        AuditLog::log(
            'PASSWORD_RESET',
            'User',
            $user->id,
            ['password_changed' => true],
            ['password_changed' => true],
            "Password reset via email link: {$user->email}",
            $user->name
        );

        Log::info("Password reset completed: {$user->email}");

        return redirect('/login')
            ->with('success', 'Password reset successfully. Please log in.');
    }

    /**
     * Display change password form (for first login)
     * 
     * GET /change-password
     * Returns: change-password.blade.php view
     * 
     * Requires: User must be logged in
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Handle password change on first login
     * 
     * POST /change-password
     * 
     * Logic:
     * 1. Validate current password
     * 2. Validate new password meets policy
     * 3. Update password
     * 4. Clear is_first_login flag
     * 5. Redirect to dashboard
     */
    public function changePassword(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,16}$/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter and one number. Special characters (@$!%*?&) are allowed.',
        ]);

        $user = Auth::user();

        // Verify current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->update([
            'password' => $validated['password'], // Hashed automatically
            'is_first_login' => false,
            'must_change_password' => false,
        ]);

        // Log audit
        AuditLog::log(
            'PASSWORD_CHANGE',
            'User',
            $user->id,
            ['password_changed' => true],
            ['password_changed' => true],
            "User changed password: {$user->email}",
            $user->name
        );

        Log::info("First login password change: {$user->email}");

        return redirect('/dashboard')
            ->with('success', 'Password changed successfully');
    }
}
