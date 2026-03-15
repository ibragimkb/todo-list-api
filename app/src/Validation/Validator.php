<?php declare(strict_types=1);

namespace App\Validation;

use App\Core\Response;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data  = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {

                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = 'Field is required';
                }

                if ($rule === 'string' && $value !== null && !is_string($value)) {
                    $this->errors[$field][] = 'Must be a string';
                }

                if ($rule === 'integer' && $value !== null && !is_int($value)) {
                    $this->errors[$field][] = 'Must be an integer';
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (is_string($value) && strlen($value) < $min) {
                        $this->errors[$field][] = "Minimum length is $min";
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (is_string($value) && strlen($value) > $max) {
                        $this->errors[$field][] = "Maximum length is $max";
                    }
                }

                if (str_starts_with($rule, 'in:')) {
                    $allowed = explode(',', substr($rule, 3));
                    if ($value !== null && !in_array($value, $allowed)) {
                        $this->errors[$field][] = 'Invalid value';
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validateOrFail(): void
    {
        if (!$this->validate()) {
            Response::error('Validation failed', 422, $this->errors);
            exit;
        }
    }
}

