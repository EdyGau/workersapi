<?php

namespace App\Service;

use App\Entity\Worker;
use App\Entity\Gender;
use App\Factory\WorkerFactory;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkerService
{
    private WorkerFactory $workerFactory;
    private EntityManagerInterface $entityManager;
    private WorkerRepository $workerRepository;
    private SerializerInterface $serializer;
    private FormFactoryInterface $formFactory;

    private ValidatorInterface $validator;


    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        WorkerRepository $workerRepository,
        WorkerFactory $workerFactory,
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->workerRepository = $workerRepository;
        $this->serializer = $serializer;
        $this->workerFactory = $workerFactory;
        $this->formFactory = $formFactory;
        $this->validator = $validator;
    }

    /**
     * Adds a new worker based on the provided data.
     *
     * @throws Exception
     */
    public function addWorker($data): JsonResponse
    {
        $genderName = $data['gender'];
        $gender = $this->getGender($genderName);

        $worker = $this->workerFactory->create(
            new Worker(),
            $data['name'],
            $data['surname'],
            $data['email'],
            $data['password'],
            $data['repassword'],
            $data['birthdate'],
            $data['pesel'],
            $gender
        );

        $this->entityManager->persist($worker);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'New worker added successfully'], Response::HTTP_CREATED);
    }

    /**
     * @param Worker $worker
     * @param array $data
     * @return mixed
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function updateWorker(Worker $worker, array $data): mixed
    {
        $genderName = $data['gender'];
        $gender = $this->getGender($genderName);

        $this->workerFactory->create(
            $worker,
            $data['name'],
            $data['surname'],
            $data['email'],
            $data['password'],
            $data['repassword'],
            $data['birthdate'],
            $data['pesel'],
            $gender
        );

        $this->entityManager->persist($worker);
        $this->entityManager->flush();

        return $this->serializer->normalize($worker, null, ['groups' => 'worker:update']);
    }

    /**
     * Checks if a worker with the provided PESEL number or email already exists in the database.
     *
     * @param string $pesel
     * @param string $email
     * @return bool
     */
    public function isWorkerExist(string $pesel, string $email): bool
    {
        return $this->workerRepository->isWorkerExist($pesel, $email);
    }

    /**
     * @param string $genderName
     * @return Gender
     * @throws EntityNotFoundException
     */
    private function getGender(string $genderName): Gender
    {
        $gender = $this->entityManager->getRepository(Gender::class)->findOneBy(['name' => $genderName]);

        if (!$gender) {
            throw new EntityNotFoundException('Gender with the given name not found.');
        }

        return $gender;
    }
}
