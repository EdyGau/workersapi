<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PeselNumber extends Constraint
{
    public string $message = 'Invalid pesel number.';
}