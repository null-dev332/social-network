<?php

namespace App\Http\Requests;

use App\Domain\Models\User;
use App\Rules\CleanText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'bio' => ['nullable', 'string', 'max:500', new CleanText],
            'role' => ['required', 'in:user,manager,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'email.email' => "That email doesn't look right.",
            'email.unique' => 'This email is already taken.',
            'role.in' => 'Unknown role.',
        ];
    }
}
