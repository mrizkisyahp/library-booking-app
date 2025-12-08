<?php

namespace App\Core\Exceptions;

use App\Core\Validator\Validator;

class ValidationException extends \Exception
{
    protected array $errors;
    private Validator $validator;

    public function __construct(Validator $validator, array $errors, string $message = 'Validation Failed', int $code = 422)
    {
        $this->validator = $validator;
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }
}