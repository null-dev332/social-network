<?php

namespace App\Http\Requests;

use App\Rules\CleanText;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'content' => ['required', 'string', 'min:1', 'max:2000', new CleanText],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Write something first.',
            'content.max' => 'Posts can be at most 2000 characters.',
            'image.image' => 'The file has to be an image.',
            'image.max' => 'The image can be at most 2 MB.',
        ];
    }
}
