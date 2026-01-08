<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\AuditLog;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountUnlockedMail;
use App\Mail\NewUserAccountMail;
use App\Mail\AdminPasswordResetMail;
use App\Imports\StaffImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Display a listing of users/staff.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('role', 'department');

        // Search by name, email, or staff_id
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('role', function ($q) {
                $q->where('name', request('role'));
            });
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'locked') {
                $query->whereNotNull('locked_until')
                    ->where('locked_until', '>', now());
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        // Handle sort by role name or department name if needed, else default to column
        if ($sortBy === 'role') {
            // Basic sort, can be improved with joins
            $query->orderBy('role_id', $request->get('direction', 'desc'));
        } else {
            $query->orderBy($sortBy, $request->get('direction', 'desc'));
        }


        $users = $query->paginate(25);
        $roles = Role::all();
        $departments = Department::active()->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new user (and staff).
     */
    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::where('name', '!=', 'TaskForceOwner')->get();
        $departments = Department::active()->get();
        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created user/staff in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Combined validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            // 'staff_id' => auto-generated
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'employment_status' => ['nullable', 'in:Permanent,Contract,Visiting,Inactive'],
            'notes' => 'nullable|string',
        ]);

        // Get Role Slug for server-side validation logic
        $role = Role::find($validated['role_id']);
        if (in_array($role->slug, ['lecturer', 'hod'])) {
            if (empty($validated['department_id'])) {
                return back()->withInput()->withErrors(['department_id' => 'The department field is required for Lecturers and HODs.']);
            }
        }

        // Generate password if not provided? Usually admin creates user implies generated password or set one.
        // Logic from StaffController: generated. Logic from UserController: input 'password' validation was missing in my read but likely uses Request class.
        // Let's generate a random password like StaffController did.
        $tempPassword = Str::random(12);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'first_name' => explode(' ', $validated['name'])[0], // Basic split
                'last_name' => count(explode(' ', $validated['name'])) > 1 ? substr(strstr($validated['name'], " "), 1) : '',
                'email' => $validated['email'],
                'password' => Hash::make($tempPassword),
                'role_id' => $validated['role_id'],
                'staff_id' => $this->generateStaffId(),
                'department_id' => $validated['department_id'] ?? null,
                'employment_status' => $validated['employment_status'] ?? 'Permanent',
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
                'is_first_login' => true,
                'created_by' => auth()->id(),
            ]);

            // Sync Role flags logic (if role implies flags)
            // Or flags imply role? Let's stick to input flags for now.

            // Send email
            try {
                Mail::to($user)->send(new NewUserAccountMail($user, $tempPassword));
            } catch (\Exception $e) {
                // Log error
            }

            AuditLog::log(
                'create',
                'User',
                $user->id,
                null,
                $user->toArray(),
                "Created user account for {$user->name}"
            );

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully. Login details sent via email.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $user->load('role', 'department');
        $roles = Role::all();
        $departments = Department::active()->get();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if (auth()->id() === $user->id && $request->has('role_id') && $request->role_id != $user->role_id) {
            return back()->with('warning', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            // staff_id is immutable
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'employment_status' => ['nullable', 'in:Permanent,Contract,Visiting,Inactive'],
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Role Validation for Department
        $role = Role::find($validated['role_id']);
        if (in_array($role->slug, ['lecturer', 'hod'])) {
            if (empty($validated['department_id'])) {
                return back()->withInput()->withErrors(['department_id' => 'The department field is required for Lecturers and HODs.']);
            }
        }

        try {
            DB::beginTransaction();

            $oldValues = $user->toArray();

            // Update names
            $parts = explode(' ', $validated['name']);
            $firstName = $parts[0];
            $lastName = count($parts) > 1 ? substr(strstr($validated['name'], " "), 1) : $user->last_name;

            $user->update([
                'name' => $validated['name'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
                // 'staff_id' => ... immutable
                'department_id' => $validated['department_id'] ?? null,
                'employment_status' => $validated['employment_status'],
                'notes' => $validated['notes'],
                'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
                'updated_by' => auth()->id(),
            ]);

            AuditLog::log(
                'UPDATE',
                'User',
                $user->id,
                $oldValues,
                $user->fresh()->toArray(),
                "Updated user: {$user->name}",
                $user->name
            );

            DB::commit();

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', "User '{$user->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Change user role (Quick Action).
     */
    public function changeRole(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role_id' => ['required', Rule::exists('roles', 'id')],
        ]);

        try {
            $user->update(['role_id' => $validated['role_id']]);
            return back()->with('success', "Role changed successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error changing role: ' . $e->getMessage());
        }
    }

    /**
     * Show password reset form.
     */
    public function showResetPasswordForm(User $user)
    {
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.accepted' => 'You must confirm password reset.',
        ]);

        try {
            // Generate unique token
            $token = Str::random(32);

            // Store token in cache (expires in 1 hour)
            cache()->put("password_reset_{$user->id}", $token, now()->addHour());

            // Send email with TOKEN
            Mail::to($user->email)->send(new AdminPasswordResetMail($user, $token));

            return back()->with('success', "A password reset link has been emailed to '{$user->name}'.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error sending reset link: ' . $e->getMessage());
        }
    }

    /**
     * Unlock a locked user account.
     */
    public function unlockAccount(Request $request, User $user)
    {
        if (!$user->isLocked()) {
            return back()->with('info', 'This account is not currently locked.');
        }

        try {
            $user->unlock();
            Mail::to($user->email)->send(new AccountUnlockedMail($user));
            return back()->with('success', "Account unlocked for '{$user->name}' successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error unlocking account: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'Activated' : 'Deactivated';

        return back()->with('success', "User account {$status} successfully.");
    }

    /**
     * Force password change.
     */
    public function forcePasswordChange(User $user)
    {
        $user->update(['must_change_password' => true]);
        return back()->with('success', "User will be required to change password on next login.");
    }

    /**
     * Show user details.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        $user->load('role', 'department', 'workloadSubmissions', 'performanceScores'); // Load related data

        return view('admin.users.show', compact('user'));
    }

    /**
     * Delete a user account.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->name}' deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Export users to CSV.
     */
    public function export()
    {
        $users = User::with('role', 'department')->get();

        $csv = "Name,Email,Role,Department,Status,Last Login,Created Date\n";

        foreach ($users as $user) {
            $status = !$user->is_active ? 'Inactive' : ($user->isLocked() ? 'Locked' : 'Active');
            $lastLogin = $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never';
            $dept = $user->department ? $user->department->name : '-';

            $csv .= "\"{$user->name}\",\"{$user->email}\",\"{$user->role->name}\",\"{$dept}\",\"{$status}\",\"{$lastLogin}\",\"{$user->created_at->format('Y-m-d')}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Show the bulk import form.
     */
    public function import()
    {
        $this->authorize('create', User::class);
        return view('admin.users.import');
    }

    /**
     * Handle the bulk import upload.
     */
    public function upload(Request $request)
    {
        $this->authorize('create', User::class);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new StaffImport, $request->file('file'));
            return redirect()->route('admin.users.index')->with('success', 'Users/Staff imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->with('error', 'Validation failed for some rows.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
    /**
     * Generate a unique Staff ID.
     * Format: S + 5 digits (e.g., S12345)
     */
    private function generateStaffId()
    {
        do {
            $randomDigits = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $staffId = 'S' . $randomDigits;
        } while (User::where('staff_id', $staffId)->exists());

        return $staffId;
    }
}
