<?php

namespace App\Helper;

use Exception;
use InvalidArgumentException;

class PeselNumberHelper
{
    /**
     * Generates a random PESEL number based on the provided birthdate and gender.
     *
     * @param string $gender The gender of the person ('Man' for male, 'Woman' for female).
     * @return string The generated PESEL number.
     * @throws Exception
     */
    public function generateRandomPeselNumber($birthdate, string $gender): string
    {
        $birthYear = intval($birthdate->format('Y')) % 100;
        $firstSixDigits = sprintf('%02d%02d%02d', $birthYear, intval($birthdate->format('m')), intval($birthdate->format('d')));

        $randomDigits = '';
        for ($i = 0; $i < 3; $i++) {
            $digit = rand(0, 9);
            $randomDigits .= $digit;
        }

        $orderNumber = intval($randomDigits . $this->getGenderNumber($gender));

        return $firstSixDigits . $orderNumber . '1';
    }

    /**
     * Returns the gender number.
     *
     * @param string $gender The gender of the person ('Man' for male, 'Woman' for female).
     * @throws InvalidArgumentException If an invalid gender is provided.
     */
    private function getGenderNumber(string $gender): int
    {
        if ($gender === 'Woman') {
            return rand(1, 9) * 2 + 1;
        } elseif ($gender === 'Man') {
            return rand(0, 4) * 2 + 1;
        } else {
            throw new InvalidArgumentException('Invalid gender. Accepted values are "Man" or "Woman".');
        }
    }
}