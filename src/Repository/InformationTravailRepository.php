<?php

namespace App\Repository;

use App\Entity\InformationTravail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InformationTravail|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationTravail|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationTravail[]    findAll()
 * @method InformationTravail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationTravailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationTravail::class);
    }

    // /**
    //  * @return InformationTravail[] Returns an array of InformationTravail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformationTravail
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
