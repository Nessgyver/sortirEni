<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }

     /**
      * @return Lieu[] Returns an array of Lieu objects
      */
    public function findByVilleId($ville)
    {
        $lieux = $this->createQueryBuilder('l')
            ->andWhere('l.ville = :ville')
            ->setParameter(":ville", $ville)
            ->getQuery()
            ->getArrayResult()
        ;
        return $lieux;
    }
    public function findById($lieuId)
    {
        $lieu = $this->createQueryBuilder('l')
            ->andWhere('l.id = :lieuId')
            ->setParameter(":lieuId", $lieuId)
            ->getQuery()
            ->getArrayResult()
        ;
        return $lieu;
    }

    /*
    public function findOneBySomeField($value): ?Lieu
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
