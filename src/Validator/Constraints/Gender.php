<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Gender extends Constraint
{
    public string $message = 'Only Man/Woman are allowed.';
}