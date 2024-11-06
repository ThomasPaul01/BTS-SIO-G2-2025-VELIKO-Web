<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsEmailValideValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var IsEmailValide $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Vérifie si l'adresse contient un '@' et un "." apres
        if (str_contains($value, '@')) {
            $parts = explode('@', $value);

            if (isset($parts[1]) && str_contains($parts[1], '.')) {
                return ;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
