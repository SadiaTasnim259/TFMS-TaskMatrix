<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find department by name (case insensitive)
        $department = Department::where('name', $row['department'])->first();

        if (!$department) {
            return null;
        }

        // Determine Role
        // Default to Lecturer if not specified
        $roleName = 'Lecturer';
        if ($this->mapBoolean($row['is_hod'] ?? false)) {
            $roleName = 'HOD';
        } elseif ($this->mapBoolean($row['is_program_coordinator'] ?? false)) {
            $roleName = 'PSM';
        }

        $role = Role::where('name', $roleName)->first();
        $roleId = $role ? $role->id : 4; // 4 = Lecturer assumption or fetch dynamically

        $password = Str::random(12);

        return new User([
            'staff_id' => $row['staff_id'],
            'name' => $row['first_name'] . ' ' . $row['last_name'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'role_id' => $roleId,
            'department_id' => $department->id,
            'employment_status' => $this->mapStatus($row['employment_status'] ?? 'Permanent'),
            'is_active' => true,
            'is_first_login' => true,
        ]);

        // Note: This does not send emails. We might need an event listener or handle it in OnAfterImport.
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|unique:users,staff_id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'department' => 'required|exists:departments,name',
        ];
    }



    private function mapStatus($status)
    {
        $map = [
            'Permanent' => 'Permanent',
            'Contract' => 'Contract',
            'Visiting' => 'Visiting',
        ];
        return $map[$status] ?? 'Permanent';
    }

    private function mapBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
