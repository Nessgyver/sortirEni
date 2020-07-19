<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security = $security;
    }

    public function findByFilters($data)
    {
        $currentUser = $this->security->getUser();
        $motCle = $data['nomSortie'];
        $campus = $data['campus'];
        $dataFiltres = $data['Filtres'];
        $dataMotCle = $data['nomSortie'];
        $dataDateDebut = $data['dateDebut'];
        $dataDateFin = $data['dateFin'];

        $qb = $this->createQueryBuilder('s');
        //$orModule = $qb->expr()->orX();
        //$orModule->add($qb->expr()->eq('s.etat', ':etat'));
        //$orModule->add($qb->expr()->eq('s.organisateur', ':currentUser'));
        //$qb -> andWhere('s.etat != :etat')
        //-> orWhere($orModule)
        //-> setParameter('etat', Sortie::CREATE)



        //Gestion intervalle de dates
        if ($dataDateFin && $dataDateDebut) {
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dataDateDebut)
                ->setParameter('dateFin', $dataDateFin);

        }

        //Gestion pour un mot clé
        if ($dataMotCle) {
            $qb->andWhere('s.nom LIKE :motCle')
                ->setParameter('motCle', "%$motCle%");

        }

        /**
         * @todo : gestion campus + gestion cas particuliers fitlres + unsubscribed
         */
        /*
        //Gestion pour campus
        if ($campus)
        {
            $qb->innerJoin('s.organisateur', 'p')
                ->andWhere('p.campus = :campus')
                ->setParameter('campus', $campus);
        }
        */

        //Gestion des filtres
        if ($dataFiltres)
        {
            //Sorties dont je suis l'organisateur
            if (in_array(0, $dataFiltres)) {
                $qb->orWhere('s.organisateur = :currentUser');
                $qb->join('s.organisateur', 'o')
                    ->addSelect('o');


            }

            //Sorties dont je suis inscrit
            if (in_array(1, $dataFiltres))
            {
                $qb->leftJoin('s.inscriptions', 'i')
                    ->orWhere('i.participant = :currentUser');
            }


            //Sorties dont je ne suis pas inscrit
            if (in_array(2, $dataFiltres))
            {

            }

            //Sorties passées
            if (in_array(3, $dataFiltres)) {
                $qb->orWhere('s.etat = :etat')
                    ->setParameter('etat', Sortie::FINISHED)
                    ->join('s.etat', 'e')
                    ->addSelect('e');
            }

            if (in_array(0, $dataFiltres) || in_array(1, $dataFiltres) || in_array(2, $dataFiltres) || in_array(3, $dataFiltres))
            {
                $qb -> setParameter('currentUser', $currentUser);
            }
        }



        return $qb->getQuery()->getResult();
    }



    public function findUnsubscribed()
    {
        $currentUser = $this->security->getUser();
        $qb = $this->createQueryBuilder('s');

        //Sorties auxquelles je suis inscrit
        $qb->leftJoin('s.inscriptions', 'i')
            ->andWhere('i.participant = :currentUser')
            ->setParameter('currentUser', $currentUser);
        $listeSortiesInscrit = $qb->getQuery()->getArrayResult();

        $qb2 = $this->createQueryBuilder('s2');
        $qb2 ->addSelect('s2')
            ->where($qb2->expr()->notIn('s2.id', ':listeSortiesInscrit'))
            ->setParameter('listeSortiesInscrit', $listeSortiesInscrit);

        return $qb2->getQuery()->getResult();
    }

    /*
    public function findByCampus($campus)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->innerJoin('s.organisateur', 'o')
            ->andWhere('o.campus = :campus')
            ->setParameter('campus', $campus);
        return $qb->getQuery()->getResult();
    }
    */


    /*
    public function findByOrganisateur()
    {
        $currentUser = $this->security->getUser();
        $qb = $this->createQueryBuilder('s');
        $qb -> andWhere('s.organisateur = :currentUser')
            ->setParameter('currentUser', $currentUser);
        $qb->join('s.organisateur', 'o');
        return $qb->getQuery()->getResult();
    }

    public function findByArchivedSortie()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.etat = :etat')
            ->setParameter('etat', '6')
            ->join('s.etat', 'e');

        return $qb->getQuery()->getResult();
    }

    public function findByMotCle($motCle)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.nom LIKE :motCle')
            ->setParameter('motCle', "%$motCle%");

        return $qb->getQuery()->getResult();
    }

    public function findSubscribed()
    {
        $currentUser = $this->security->getUser();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.inscriptions', 'i')
            ->join('i.participant', 'p')
            ->andWhere('p = :currentUser')
            ->setParameter('currentUser', $currentUser);

        return $qb->getQuery()->getResult();
    }





    public function findByDateInterval(DateTime $dateDebut, DateTime $dateFin)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        return $qb->getQuery()->getResult();
    }

    public function findAllWhileConnected()
    {
        $currentUser = $this->security->getUser();

        $qb= $this->createQueryBuilder('s');


        return $qb->getQuery()->getResult();

    }*/




    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
