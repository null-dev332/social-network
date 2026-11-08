<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

// custom validation rule, blocks a few rude words
class CleanText implements ValidationRule
{
    private array $bannedWords = [
        'idiot',
        'moron',
        'retard',
        'dumbass',
        'asshole',
        'bitch',
        'fuck',
        'shit',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $text = mb_strtolower($value);

        foreach ($this->bannedWords as $word) {
            if (str_contains($text, $word)) {
                $fail('Please keep it friendly, that word is not allowed.');

                return;
            }
        }
    }
}
