<?php

namespace App\Validator;

use App\Validator\Constraints\PeselNumber as PeselNumberValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Validator\Constraints\Gender as GenderValidator;

class WorkerValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates the provided gender.
     *
     * @param string $genderName
     * @return void
     */
    public function validateGender(string $genderName): void
    {
        $genderViolations = $this->validator->validate($genderName, new GenderValidator());
        if (count($genderViolations) > 0) {
            $errors = [];
            foreach ($genderViolations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errors));
        }
    }

    /**
     * Validates the provided PESEL number.
     *
     * @param string $pesel
     * @param string $birthdate
     * @param $gender
     * @return void
     */
    public function validatePesel(string $pesel, string $birthdate, $gender): void
    {
        $value = [
            'pesel' => $pesel,
            'birthdate' => $birthdate,
            'gender' => $gender
        ];

        $peselViolations = $this->validator->validate($value, new PeselNumberValidator());
        if (count($peselViolations) > 0) {
            $errors = [];
            foreach ($peselViolations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errors));
        }
    }

    /**
     * Validates the provided number.
     *
     * @param string $password
     * @param string $repassword
     * @return void
     */
    public function validatePassword(string $password, string $repassword): void
    {
        if ($password !== $repassword) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Invalid password format. Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit.');
        }
    }
}
