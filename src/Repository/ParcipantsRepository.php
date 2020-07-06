<?php

namespace App\Repository;

use App\Entity\Parcipants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parcipants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parcipants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parcipants[]    findAll()
 * @method Parcipants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcipantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parcipants::class);
    }

    // /**
    //  * @return Parcipants[] Returns an array of Parcipants objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Parcipants
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
