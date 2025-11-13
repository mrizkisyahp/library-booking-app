<?php

namespace App\Core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_NUMBER = 'number';
    public const RULE_UNIQUE = 'unique';

    public array $errors = [];

    public function loadData($data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = is_string($value) ? trim($value) : $value;
            }
        }
    }

    abstract public function rules(): array;

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute} ?? null;

            foreach ($rules as $rule) {
                $ruleName = is_string($rule) ? $rule : $rule[0];

                if ($ruleName === self::RULE_REQUIRED && ($value === null || $value === '')) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                    continue;
                }

                if ($ruleName === self::RULE_EMAIL && $value) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addErrorForRule($attribute, self::RULE_EMAIL);
                        continue;
                    }

                    $domain = strtolower(substr(strrchr($value, '@'), 1));
                    $allowed = ['stu.pnj.ac.id', 'pnj.ac.id'];
                    $departments = ['akuntansi', 'grafika', 'tik', 'mesin', 'sipil', 'bisnis', 'elektro'];

                    $valid = in_array($domain, $allowed, true)
                        || preg_match('/^(' . implode('|', $departments) . ')\\.pnj\\.ac\\.id$/', $domain);

                    if (!$valid) {
                        $this->addError($attribute, 'Use a valid PNJ email (e.g. @stu.pnj.ac.id or @<dept>.pnj.ac.id).');
                        continue;
                    }
                }

                if ($ruleName === self::RULE_MIN && isset($rule['min']) && $value !== null) {
                    if (mb_strlen((string)$value) < (int)$rule['min']) {
                        $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                    }
                }

                if ($ruleName === self::RULE_MAX && isset($rule['max']) && $value !== null) {
                    if (mb_strlen((string)$value) > (int)$rule['max']) {
                        $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                    }
                }

                if ($ruleName === self::RULE_MATCH && isset($rule['match'])) {
                    $matchAttr = $rule['match'];
                    $matchVal  = $this->{$matchAttr} ?? null;
                    if ($value !== $matchVal) {
                        $this->addErrorForRule($attribute, self::RULE_MATCH, ['match' => $matchAttr]);
                    }
                }

                if ($ruleName === self::RULE_NUMBER && $value !== null && $value !== '') {
                    if (!ctype_digit((string)$value)) {
                        $this->addErrorForRule($attribute, self::RULE_NUMBER);
                    }
                }

                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = App::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr LIMIT 1");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();

                    $record = $statement->fetchObject();
                    if ($record) {
                        $primary = $className::primaryKey();
                        $exceptId = $rule['except'] ?? null;
                        if ($exceptId && (int)$record->{$primary} === (int)$exceptId) {
                            continue;
                        }
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $attribute, string $rule, $params = []): void
    {
        $message = $this->errorMessages()[$rule] ?? 'Invalid value';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", (string)$value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'Use a valid PNJ email (e.g. @stu.pnj.ac.id or @dept.pnj.ac.id)',
            self::RULE_MIN => 'Minimum {min} characters required',
            self::RULE_MAX => 'Maximum {max} characters allowed',
            self::RULE_MATCH => 'Must match {match} field',
            self::RULE_NUMBER => 'Must contain digits only',
            self::RULE_UNIQUE => 'This value is already registered',
        ];
    }

    public function hasError(string $attribute): bool
    {
        return !empty($this->errors[$attribute] ?? []);
    }

    public function getFirstError(string $attribute): ?string
    {
        return $this->errors[$attribute][0] ?? null;
    }

    public function getAllErrors(): array {
        return $this->errors;
    }
}
