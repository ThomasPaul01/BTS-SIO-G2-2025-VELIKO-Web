<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsAdultValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var IsAdult $constraint */

        if (null === $value || '' === $value) {
            return;
        }
        //verifier si l'user est majeur
        $now = new \DateTime();
        $interval = $now->diff($value);

        //gérer le cas d'une personne qui vient d'être majeure
        if($interval->y > 18 || ($interval->y === 18 && ($interval->m > 0 || $interval->d > 0))){
            return;
        }
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
