<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Worker;
use App\Factory\WorkerFactory;
use App\Helper\PeselNumberHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory as FakerFactory;

class WorkerFixtures extends Fixture
{
    private WorkerFactory $workerFactory;
    private PeselNumberHelper $peselNumberHelper;

    public function __construct(WorkerFactory $workerFactory, PeselNumberHelper $peselNumberHelper)
    {
        $this->workerFactory = $workerFactory;
        $this->peselNumberHelper = $peselNumberHelper;
    }

    /**
     * Data fixtures for generating random worker data.
     * Default password for all workers is set to 'Admin1234*', but it can be changed to any other random text.
     *
     * @param ObjectManager $manager
     * @param int $size
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager, int $size = 100): void
    {
        $this->workerFactory->setSkipValidation(false);

        $faker = FakerFactory::create();

        $maleGender = new Gender();
        $maleGender->setName('Man');
        $manager->persist($maleGender);

        $femaleGender = new Gender();
        $femaleGender->setName('Woman');
        $manager->persist($femaleGender);

        $birthdate = $faker->dateTimeThisCentury;
        $formattedBirthdate = $birthdate->format('Y-m-d');

        for ($i = 0; $i < $size; $i++) {
            $gender =  $faker->randomElement([$maleGender, $femaleGender]);
            $pesel = $this->peselNumberHelper->generateRandomPeselNumber($birthdate, $gender);
            $worker = $this->workerFactory->create(
                new Worker(),
                $faker->firstName,
                $faker->lastName,
                $faker->email,
                'Admin1234*',
                'Admin1234*',
                $formattedBirthdate,
                $pesel,
                $gender
            );

            $manager->persist($worker);
        }

        $manager->flush();
        $this->workerFactory->setSkipValidation(true);
    }
}
