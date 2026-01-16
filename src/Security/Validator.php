<?php

namespace Framework\Security;

class Validator {
    protected $data;
    protected $rules;
    protected $errors = [];

    public function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate() {
        foreach ($this->rules as $field => $rulesString) {
            $rules = explode('|', $rulesString);

            foreach ($rules as $rule) {
                $this->validateRule($field, $rule, $this->data[$field] ?? null);
            }
        }

        return empty($this->errors);
    }

    protected function validateRule($field, $rule, $value) {
        $parts = explode(':', $rule);
        $rule = $parts[0];
        $params = $parts[1] ?? null;

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "$field is required");
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email");
                }
                break;
            case 'min':
                if ($value && strlen($value) < $params) {
                    $this->addError($field, "$field must be at least $params characters");
                }
                break;
            case 'max':
                if ($value && strlen($value) > $params) {
                    $this->addError($field, "$field must not exceed $params characters");
                }
                break;
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->addError($field, "$field must be numeric");
                }
                break;
            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "$field must be a valid URL");
                }
                break;
            case 'confirmed':
                $confirmationField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmationField] ?? null)) {
                    $this->addError($field, "$field confirmation does not match");
                }
                break;
            case 'unique':
                // Implementation would check database
                break;
            case 'regex':
                if ($value && !preg_match($params, $value)) {
                    $this->addError($field, "$field format is invalid");
                }
                break;
        }
    }

    protected function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function errors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }
}
