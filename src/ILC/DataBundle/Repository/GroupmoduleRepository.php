<?php
namespace ILC\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * GroupmoduleRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom
 * repository methods below.
 */
class GroupmoduleRepository extends EntityRepository
{

	public function getAllQuery($cache = true)
	{
		$qb = $this->createQueryBuilder('gm')->leftJoin('gm.modules', 'm')->leftJoin('m.stagiaires', 'ms')->leftJoin('m.sessions', 'sf')->leftJoin('sf.stagiaires', 'sfs')->orderBy('gm.name', 'ASC');
		$query = $qb->getQuery();
		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}
		return $query;
	}

	public function getAll($cache = true)
	{
		$query = $this->getAllQuery($cache);
		return $query->getResult();
	}
}
