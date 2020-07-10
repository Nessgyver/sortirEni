<?php

namespace App\Repository;

use App\Entity\PhotoParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhotoParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoParticipant[]    findAll()
 * @method PhotoParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoParticipant::class);
    }

    // /**
    //  * @return PhotoParticipant[] Returns an array of PhotoParticipant objects
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
    public function findOneBySomeField($value): ?PhotoParticipant
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
