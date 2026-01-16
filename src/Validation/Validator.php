<?php

namespace Framework\Validation;

/**
 * Input validation engine
 */
class Validator
{
    /**
     * Data to validate
     *
     * @var array
     */
    protected array $data;

    /**
     * Validation rules
     *
     * @var array
     */
    protected array $rules;

    /**
     * Validation errors
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Create a new validator
     *
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    /**
     * Run validation
     *
     * @return void
     */
    protected function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);

            foreach ($rules as $rule) {
                $this->validateRule($field, $rule);
            }
        }
    }

    /**
     * Validate a single rule
     *
     * @param string $field
     * @param string $rule
     * @return void
     */
    protected function validateRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;
        [$ruleName, $parameters] = $this->parseRule($rule);

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "{$field} is required");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$field} must be a valid email");
                }
                break;

            case 'min':
                $min = $parameters[0] ?? 0;
                if (!empty($value) && strlen((string)$value) < $min) {
                    $this->addError($field, "{$field} must be at least {$min} characters");
                }
                break;

            case 'max':
                $max = $parameters[0] ?? 255;
                if (!empty($value) && strlen((string)$value) > $max) {
                    $this->addError($field, "{$field} must not exceed {$max} characters");
                }
                break;

            case 'confirmed':
                $confirmed = $this->data["{$field}_confirmation"] ?? null;
                if ($value !== $confirmed) {
                    $this->addError($field, "{$field} confirmation does not match");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "{$field} must be numeric");
                }
                break;

            case 'string':
                if (!empty($value) && !is_string($value)) {
                    $this->addError($field, "{$field} must be a string");
                }
                break;

            case 'array':
                if (!empty($value) && !is_array($value)) {
                    $this->addError($field, "{$field} must be an array");
                }
                break;

            case 'unique':
                $this->validateUnique($field, $value, $parameters);
                break;

            case 'exists':
                $this->validateExists($field, $value, $parameters);
                break;

            case 'regex':
                $pattern = $parameters[0] ?? '';
                if (!empty($value) && !preg_match($pattern, $value)) {
                    $this->addError($field, "{$field} format is invalid");
                }
                break;
        }
    }

    /**
     * Parse a rule string
     *
     * @param string $rule
     * @return array
     */
    protected function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$name, $params] = explode(':', $rule, 2);
            return [$name, explode(',', $params)];
        }

        return [$rule, []];
    }

    /**
     * Validate unique rule
     *
     * @param string $field
     * @param mixed $value
     * @param array $parameters
     * @return void
     */
    protected function validateUnique(string $field, $value, array $parameters): void
    {
        if (empty($value) || empty($parameters)) {
            return;
        }

        $table = $parameters[0] ?? '';
        $column = $parameters[1] ?? $field;

        $exists = db()->table($table)
            ->where($column, $value)
            ->count() > 0;

        if ($exists) {
            $this->addError($field, "{$field} already exists");
        }
    }

    /**
     * Validate exists rule
     *
     * @param string $field
     * @param mixed $value
     * @param array $parameters
     * @return void
     */
    protected function validateExists(string $field, $value, array $parameters): void
    {
        if (empty($value) || empty($parameters)) {
            return;
        }

        $table = $parameters[0] ?? '';
        $column = $parameters[1] ?? $field;

        $exists = db()->table($table)
            ->where($column, $value)
            ->count() > 0;

        if (!$exists) {
            $this->addError($field, "{$field} does not exist");
        }
    }

    /**
     * Add an error
     *
     * @param string $field
     * @param string $message
     * @return void
     */
    protected function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    /**
     * Check if validation failed
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Check if validation passed
     *
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get errors for a field
     *
     * @param string $field
     * @return array
     */
    public function getErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Get validated data
     *
     * @return array
     */
    public function validated(): array
    {
        $validated = [];

        foreach (array_keys($this->rules) as $field) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }
}
