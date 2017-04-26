<?php
namespace ILC\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * StagiaireRepository
 * This class was generated by the Doctrine ORM.
 * Add your own custom
 * repository methods below.
 */
class StagiaireRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{

	public function loadUserByUsername($username, $cache = true)
	{
		$currentdate = new \DateTime();
		$qb = $this->createQueryBuilder('u')->select('u')->where('u.username = :username')->andWhere('u.dtcrea > :dt')->setParameter('dt', $currentdate)->setParameter('username', $username);

		$query = $qb->getQuery();

		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}

		try {
			$user = $query->getSingleResult();
		} catch (NoResultException $e) {
			$exp = new UsernameNotFoundException(sprintf('Unable to find an active User identified by "%s".', $username), 0, $e);
			$exp->setUsername($username);
			throw $exp;
		}

		return $user;
	}

	public function refreshUser(UserInterface $user)
	{
		$class = get_class($user);
		if (!$this->supportsClass($class)) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	public function supportsClass($class)
	{
		return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
	}

	public function getAllValidQuery($cache = true)
	{
		$currentdate = new \DateTime();
		$query = $this->createQueryBuilder('s')->select('s')->where('s.dtcrea > :dt')->setParameter('dt', $currentdate)->getQuery();
		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}
		return $query;
	}

	public function getAllValid($cache = true)
	{
		$query = $this->getAllValidQuery($cache);
		return $query->getResult();
	}

	public function getAllQuery($cache = true)
	{
		$dql = 'SELECT s FROM ILC\DataBundle\Entity\Stagiaire s ' . 'ORDER BY s.nom ASC, s.prenom ASC, s.dtcrea DESC';
		$query = $this->getEntityManager()->createQuery($dql);
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

	public function getAllFullQuery($cache = true)
	{
		$dql = 'SELECT DISTINCT s FROM ILC\DataBundle\Entity\Stagiaire s ' . 'LEFT JOIN s.sessions AS ss ' . 'LEFT JOIN s.modules AS sm ' . 'ORDER BY s.nom ASC, s.prenom ASC, s.dtcrea DESC';
		$query = $this->getEntityManager()->createQuery($dql);
		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}
		return $query;
	}

	public function getAllNewQuery($cache = true)
	{
		$dql = 'SELECT s FROM ILC\DataBundle\Entity\Stagiaire s ' . 'WHERE s.infosent = FALSE AND s.active = TRUE ' . 'ORDER BY s.dtcrea DESC';
		$query = $this->getEntityManager()->createQuery($dql);
		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}
		return $query;
	}

	public function getAllNew($cache = true)
	{
		$query = $this->getAllNewQuery($cache);
		return $query->getResult();
	}

	public function searchQuery($q, $cache = true)
	{
		$dql = 'SELECT DISTINCT s FROM ILC\DataBundle\Entity\Stagiaire s ' . 'LEFT JOIN s.sessions AS ss ' . 'LEFT JOIN s.modules AS sm ' . "WHERE s.username LIKE :key " . "OR s.username LIKE :key " . "OR s.email LIKE :key " . "OR s.nom LIKE :key " . "OR s.prenom LIKE :key " . "OR s.tel LIKE :key " . "OR s.nomcontact LIKE :key " . "OR s.emailcontact LIKE :key " . 'ORDER BY s.nom ASC, s.prenom ASC, s.dtcrea DESC';
		$query = $this->getEntityManager()->createQuery($dql)->setParameter('key', '%' . $q . '%');
		if ($cache) {
			$query->setCacheable('true')->useQueryCache(true)->setLifetime(60)->useResultCache(true, 60);
		}
		return $query;
	}
}