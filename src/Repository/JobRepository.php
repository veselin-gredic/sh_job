<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    /**
     * JobRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @param $value
     * @return Job|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByEmailPublished($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.email = :val')
            ->andWhere('j.status = 2')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param $value
     * @return Job|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlug($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.slug = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
