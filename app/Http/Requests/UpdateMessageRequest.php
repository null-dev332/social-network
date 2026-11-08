<?php

namespace App\Http\Requests;

use App\Rules\CleanText;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('content')) {
            $this->merge([
                'content' => trim((string) $this->input('content')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:1000', new CleanText],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Write a message first.',
            'content.max' => 'Messages can be at most 1000 characters.',
        ];
    }
}
