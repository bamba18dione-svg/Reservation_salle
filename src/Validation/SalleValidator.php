<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class SalleValidator implements ValidatorInterface
{
    private const ALLOWED_TYPES = [
        'cours',
        'informatique',
        'laboratoire',
        'amphitheatre',
        'reunion',
    ];

    public function validate(array $data): ValidationResult
    {
        $rules = [
            'nom' => v::stringType()->notEmpty()->length(2, 100),
            'batiment' => v::stringType()->notEmpty()->length(2, 100),
            'capacite' => v::intVal()->between(1, 1000),
            'type' => v::in(self::ALLOWED_TYPES),
            'active' => v::boolType(),
        ];

        return $this->validateFields($data, $rules);
    }

    private function validateFields(array $data, array $rules): ValidationResult
    {
        $acceptedData = [];
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!array_key_exists($field, $data)) {
                $errors[$field][] = 'Ce champ est obligatoire.';
                continue;
            }

            try {
                $rule->assert($data[$field]);
                $acceptedData[$field] = $data[$field];
            } catch (ValidationException $exception) {
                $errors[$field] = [$exception->getMessage()];
            }
        }

        return new ValidationResult($acceptedData, $errors);
    }
}