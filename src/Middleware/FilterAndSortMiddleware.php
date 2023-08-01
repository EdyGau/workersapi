<?php
namespace App\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class FilterAndSortMiddleware
{
    private const DEFAULT_LIMIT = 10;
    private const DEFAULT_PAGE = 1;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Decodes filters and sorting parameters from the request query string,
     *
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $filters = json_decode($request->query->get('filters', '{}'), true);
        $sorting = json_decode($request->query->get('sorting', '{}'), true);

        $limit = $request->query->getInt('limit', self::DEFAULT_LIMIT);

        $page = $request->query->getInt('page', self::DEFAULT_PAGE);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('w')
            ->from('App\Entity\Worker', 'w');

        foreach ($filters as $field => $value) {
            $queryBuilder->andWhere("w.$field = :$field");
            $queryBuilder->setParameter($field, $value);
        }

        foreach ($sorting as $field => $direction) {
            $queryBuilder->orderBy("w.$field", $direction);
        }

        $offset = ($page - 1) * $limit;
        $queryBuilder->setMaxResults($limit)->setFirstResult($offset);

        $event->getRequest()->attributes->set('query_builder', $queryBuilder);
    }
}
