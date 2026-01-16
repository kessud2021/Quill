<?php

namespace Framework\Validation;

class Rules {
    public static function required($value) {
        return !empty($value);
    }

    public static function email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function url($value) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function ip($value) {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    public static function numeric($value) {
        return is_numeric($value);
    }

    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function boolean($value) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }

    public static function min($value, $min) {
        if (is_numeric($value)) {
            return $value >= $min;
        }

        return strlen($value) >= $min;
    }

    public static function max($value, $max) {
        if (is_numeric($value)) {
            return $value <= $max;
        }

        return strlen($value) <= $max;
    }

    public static function between($value, $min, $max) {
        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }

        $length = strlen($value);
        return $length >= $min && $length <= $max;
    }

    public static function regex($value, $pattern) {
        return preg_match($pattern, $value) === 1;
    }

    public static function in($value, $values) {
        return in_array($value, $values);
    }

    public static function notIn($value, $values) {
        return !in_array($value, $values);
    }

    public static function confirmed($value, $confirmed) {
        return $value === $confirmed;
    }

    public static function date($value) {
        return strtotime($value) !== false;
    }

    public static function before($value, $date) {
        return strtotime($value) < strtotime($date);
    }

    public static function after($value, $date) {
        return strtotime($value) > strtotime($date);
    }

    public static function json($value) {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function array($value) {
        return is_array($value);
    }

    public static function string($value) {
        return is_string($value);
    }
}
