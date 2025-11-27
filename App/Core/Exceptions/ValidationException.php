<?php

namespace App\Core\Exceptions;

class ValidationException extends \Exception
{
    protected array $errors;

    public function __construct(array $errors, string $message = 'Validation Failed', int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

}