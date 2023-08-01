<?php

namespace App\Factory;

use App\Entity\Worker;
use App\Validator\WorkerValidator;
use DateTime;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkerFactory
{
    private ValidatorInterface $validator;

    private WorkerValidator $workerValidator;
    private bool $skipValidation = true;
    public function __construct(ValidatorInterface $validator, WorkerValidator $workerValidator)
    {
        $this->validator = $validator;
        $this->workerValidator = $workerValidator;
    }

    /**
     * @param bool $skipValidation
     * @return void
     */
    public function setSkipValidation(bool $skipValidation): void
    {
        $this->skipValidation = $skipValidation;
    }

    /**
     * @throws Exception
     */
    public function create(Worker $worker, $firstName, $lastName, $email, $password, $repassword, $birthdate, $pesel, $gender)
    {
        if ($this->skipValidation) {
            $this->workerValidator->validateGender($gender->getName());
            $this->workerValidator->validatePesel($pesel, $birthdate, $gender->getName());
            $this->workerValidator->validatePassword($password, $repassword);
        }

        $birthdayDate = new DateTime($birthdate);

        $worker->setName($firstName);
        $worker->setSurname($lastName);
        $worker->setEmail($email);
        $worker->setPassword($password);
        $worker->setBirthdate($birthdayDate);
        $worker->setPesel($pesel);
        $worker->setGender($gender);

        return $worker;
    }
}
