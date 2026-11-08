<?php

namespace App\Http\Requests;

use App\Rules\CleanText;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
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
            'content' => ['required', 'string', 'min:1', 'max:500', new CleanText],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Write a comment first.',
            'content.max' => 'Comments can be at most 500 characters.',
        ];
    }
}
