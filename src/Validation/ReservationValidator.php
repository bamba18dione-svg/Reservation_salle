<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $rules = [
            'salle_id' => v::intVal()->positive(),
            'responsable' => v::stringType()->notEmpty()->length(2, 120),
            'email' => v::email(),
            'motif' => v::stringType()->length(5, 255),
            'date_debut' => v::dateTime('Y-m-d H:i:s'),
            'date_fin' => v::dateTime('Y-m-d H:i:s'),
        ];

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