<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can create users - this is handled by middleware in controller
        // but we can double check here if needed.
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role_id' => ['required', 'exists:roles,id'],
            // Password is auto-generated, so we don't validate it from input
            // unless we allow admin to set it manually. 
            // The requirement says "Password field in form" [ ] ❌
            // But also "After creating user, Email contains temporary password"
            // Usually if we email a temp password, we don't ask admin to type one.
            // However, the checklist says "Password field in form".
            // I will make it optional. If provided, use it. If not, generate one.
            // Wait, the checklist says "Password validation: required, min:8, max:16, alpha_num".
            // This implies the Admin MUST type it.
            // Let's follow the checklist strictly.
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'alpha_num', // "Must be alphanumeric"
            ],
        ];
    }
}
