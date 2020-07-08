<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Inscription::class);
        $this->security = $security;
    }

    public function findBySubscribedSorties()
    {
        $currentUser = $this->security->getUser();

        $qb = $this->createQueryBuilder('i');
        $qb -> join('i.participant', 'p')
            -> join('i.sortie', 's')
            -> andWhere('p.id = :currentUser')
            -> setParameter('currentUser', $currentUser->getId());

        return $qb->getQuery()->getResult();
    }

    public function findByUnsubscribedSorties()
    {
        $currentUser = $this->security->getUser();

        $qb = $this->createQueryBuilder('i');
        $qb -> join('i.participant', 'p')
            -> join('i.sortie', 's')
            -> andWhere('p.id != :currentUser')
            -> setParameter('currentUser', $currentUser->getId());


        return $qb->getQuery()->getResult();
    }



    // /**
    //  * @return Inscription[] Returns an array of Inscription objects
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
    public function findOneBySomeField($value): ?Inscription
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
