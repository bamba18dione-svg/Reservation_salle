<?php

declare(strict_types=1);

namespace App\Validation;

final class ValidationResult
{
    public function __construct(
        private readonly array $acceptedData,
        private readonly array $errors,
    ) {
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function acceptedData(): array
    {
        return $this->acceptedData;
    }
}