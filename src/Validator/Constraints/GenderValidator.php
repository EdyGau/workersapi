<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GenderValidator extends ConstraintValidator
{
    /**
     * Validates the gender value. Allowed values: "Man" or "Woman".
     *
     * @param $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!in_array($value, ['Man', 'Woman'], true)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}