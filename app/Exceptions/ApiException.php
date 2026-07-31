<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class ApiException extends Exception
{
    public function __construct(
        string $message,
        private readonly int $internalCode = 50000,
        private readonly int $httpStatus = 500,
        private readonly array $errors = []
    ) {
        parent::__construct($message, $internalCode);
    }

    public function internalCode(): int
    {
        return $this->internalCode;
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
