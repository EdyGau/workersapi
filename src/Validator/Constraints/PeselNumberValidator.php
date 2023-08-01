<?php

namespace App\Validator\Constraints;

use DateTime;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeselNumberValidator extends ConstraintValidator
{
    /**
     * @param $value
     * @param Constraint $constraint
     * @return void
     * @throws Exception
     */
    public function validate($value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        $pesel = $value['pesel'];

        $birthdate = $value['birthdate'];
        $gender = $value['gender'];

        if (!$this->validatePeselWithDate($pesel, $birthdate) || !$this->validatePeselWithGender($pesel, $gender)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    /**
     * @param string $pesel
     * @return string
     * @throws InvalidArgumentException
     */
    private function extractGenderFromPesel(string $pesel): string
    {
        $genderDigit = (int)$pesel[9];
        $isMale = $genderDigit % 2 === 1;
        return $isMale ? 'Woman' : 'Man';
    }

    /**
     * @param string $pesel
     * @param string $gender
     * @return bool
     * @throws InvalidArgumentException
     */
    private function validatePeselWithGender(string $pesel, string $gender): bool
    {
        $peselGender = $this->extractGenderFromPesel($pesel);

        if ($peselGender !== $gender) {
            throw new InvalidArgumentException('Gender does not match the PESEL.');
        }

        return true;
    }

    /**
     * @param string $pesel
     * @param string $birthdate
     * @return bool
     * @throws Exception
     */
    private function validatePeselWithDate(string $pesel, string $birthdate): bool
    {
        $birthdayDateTime = new DateTime($birthdate);

        $year = $birthdayDateTime->format('Y');
        $lastTwoDigitsOfYear = substr($year, -2);
        $month = $birthdayDateTime->format('m');
        $day = $birthdayDateTime->format('d');

        $sixDigitsFromDate = $lastTwoDigitsOfYear . $month . $day;
        $sixDigitsFromPesel = substr($pesel, 0, 6);

        if ($sixDigitsFromDate !== $sixDigitsFromPesel) {
            throw new InvalidArgumentException(
                'The date of birth from the PESEL number does not match the provided date.'
            );
        }

        return true;
    }
}