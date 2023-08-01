<?php

namespace App\Repository;

use App\Entity\Worker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Worker>
 *
 * @method Worker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Worker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Worker[]    findAll()
 * @method Worker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class WorkerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Worker::class);
    }

    /**
     * Checks if a worker with the provided PESEL or email already exists in the database.
     *
     * @param string $pesel
     * @param string $email
     * @return bool
     */
    public function isWorkerExist(string $pesel, string $email): bool
    {
        $qb = $this->createQueryBuilder('w');
        $qb->select('COUNT(w.id)');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->eq('w.pesel', ':pesel'),
                $qb->expr()->eq('w.email', ':email')
            )
        );
        $qb->setParameters([
            'pesel' => $pesel,
            'email' => $email,
        ]);

        try {
            $count = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new \RuntimeException('Error while checking worker existence: ' . $e->getMessage());
        }

        return $count > 0;
    }
}
