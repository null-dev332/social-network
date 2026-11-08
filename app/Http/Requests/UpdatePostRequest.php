<?php

namespace App\Http\Requests;

use App\Rules\CleanText;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post && $this->user()->can('update', $post);
    }

    protected function prepareForValidation(): void
    {
        $merged = [];

        if ($this->has('content')) {
            $merged['content'] = trim((string) $this->input('content'));
        }

        if ($this->exists('remove_image')) {
            $merged['remove_image'] = $this->boolean('remove_image');
        }

        if ($merged) {
            $this->merge($merged);
        }
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:2000', new CleanText],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
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
