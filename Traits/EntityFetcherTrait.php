<?php
namespace Araneum\AdminBundle\Traits;

use Doctrine\ORM\Query;

/**
 * Class EntityFetcherTrait
 *
 * @package Araneum\AdminBundle\Traits
 */
trait EntityFetcherTrait
{
    /**
     * Get Entity by Id
     * Used for getting plain entity without any relation
     * Solution prevents memory leaks
     *
     * @param int $id Id of Entity
     * @return Object|null
     */
    public function getEntityById($id)
    {
        $qb = $this->createQueryBuilder('REPO');

        return $qb->where(
                $qb->expr()->eq('REPO.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult();
    }
}