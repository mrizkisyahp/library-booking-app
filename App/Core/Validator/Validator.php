<?php

namespace App\Core\Validator;

use App\Core\App;
use App\Core\Exceptions\ValidationException;
use PDO;

class Validator
{
    public function validate(array $data, array $rules, array $messages = []): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $rulesArray = is_array($ruleSet) ? $ruleSet : explode("|", $ruleSet);

            foreach ($rulesArray as $rule) {
                [$name, $params] = $this->parseRule($rule);
                $error = $this->applyRule($name, $value, $field, $data, $params);

                if ($error) {
                    $errors[$field][] = $messages["$field.$name"] ?? $error;
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $data;
    }

    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $param] = explode(':', $rule, 2);
            $params = explode(',', $param);
        } else {
            $name = $rule;
            $params = [];
        }
        return [$name, $params];
    }

    private function applyRule(string $name, $value, string $field, array $data, array $params): ?string
    {
        switch ($name) {
            case 'required':
                return ($value === null || $value === '') ? 'This field is required' : null;

            case 'string':
                return ($value === null || is_string($value)) ? null : 'Must be a string';

            case 'int':
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT) !== false ? null : 'Must be an integer';

            case 'numeric':
                return is_numeric($value) ? null : 'Must be a number';

            case 'email':
                return $this->validatePNJEmail($value);

            case 'min':
                $min = (int) ($params[0] ?? 0);
                return (strlen((string) $value) < $min) ? "Minimum {$min} characters." : null;

            case 'max':
                $max = (int) ($params[0] ?? 0);
                return (strlen((string) $value) > $max) ? "Maximum {$max} characters." : null;

            case 'between':
                $min = (int) ($params[0] ?? 0);
                $max = (int) ($params[1] ?? 0);
                $len = strlen((string) $value);
                return ($len < $min || $len > $max) ? "Between {$min} and {$max} characters." : null;

            case 'in':
                return in_array($value, $params, true) ? null : 'Invalid value.';

            case 'confirmed':
                $other = $data[$field . '_confirmation'] ?? null;
                return ($value === $other) ? null : 'Confirmation does not match.';

            case 'regex':
                $pattern = $params[0] ?? '';
                return (preg_match($pattern, (string) $value)) ? null : 'Invalid format.';

            case 'exists':
                return $this->validateExists($field, $value, $params);

            case 'unique':
                return $this->validateUnique($field, $value, $params);

            case 'date':
                return (strtotime((string) $value) !== false) ? null : 'Must be a valid date.';

            case 'after':
                $other = $params[0] ?? null;
                if (!$other)
                    return null;
                $otherValue = $data[$other] ?? null;
                $ts = strtotime((string) $value);
                $tsOther = strtotime((string) $otherValue);
                return ($ts !== false && $tsOther !== false && $ts > $tsOther) ? null : "Must be after {$other}.";

            case 'before':
                $other = $params[0] ?? null;
                if (!$other)
                    return null;
                $otherValue = $data[$other] ?? null;
                $ts = strtotime((string) $value);
                $tsOther = strtotime((string) $otherValue);
                return ($ts !== false && $tsOther !== false && $ts < $tsOther) ? null : "Must be before {$other}.";

            default:
                return null;
        }
    }

    private function validatePNJEmail($value): ?string
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Use a valid Email';
        }

        $domain = strtolower(substr(strrchr($value, '@'), 1));
        $allowed = ['stu.pnj.ac.id', 'pnj.ac.id', 'staff.pnj.ac.id'];
        $departments = ['akuntansi', 'grafika', 'tik', 'mesin', 'sipil', 'bisnis', 'elektro'];

        $valid = in_array($domain, $allowed, true)
            || preg_match('/^(' . implode('|', $departments) . ')\\.pnj\\.ac\\.id$/', $domain);

        return $valid ? null : 'Use a valid PNJ Email';
    }

    private function validateExists(string $field, $value, array $params): ?string
    {
        // after query builder implement this
        return false;
    }

    private function validateUnique(string $field, $value, array $params): ?string
    {
        // after query builder implement this
        return false;
    }
}