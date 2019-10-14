<?php

namespace App\Repository;

use App\Entity\CampTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CampTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampTranslation[]    findAll()
 * @method CampTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampTranslation::class);
    }

    // /**
    //  * @return CampTranslation[] Returns an array of CampTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CampTranslation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
