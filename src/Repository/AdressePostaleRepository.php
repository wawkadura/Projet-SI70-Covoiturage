<?php

namespace App\Repository;

use App\Entity\AdressePostale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdressePostale|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdressePostale|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdressePostale[]    findAll()
 * @method AdressePostale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdressePostaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdressePostale::class);
    }

    // /**
    //  * @return AdressePostale[] Returns an array of AdressePostale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdressePostale
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
