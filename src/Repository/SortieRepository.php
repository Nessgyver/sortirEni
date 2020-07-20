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
        //$orModule->add($qb->expr()->eq('s.etat', ':etat1'));
        //$orModule->add($qb->expr()->eq('s.organisateur', ':currentUser'));
        //$qb -> orWhere($orModule)
        //-> setParameter('etat1', Sortie::CREATE)
        ;


        /*
        //Gestion intervalle de dates
        if ($dataDateFin && $dataDateDebut)
        {
            echo 'intervalle date';
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dataDateDebut)
                ->setParameter('dateFin', $dataDateFin);
        }*/

        //Gestion pour un mot clé
        if ($dataMotCle != null) {
            echo 'mot clé';
            $qb->andWhere('s.nom LIKE :motCle')
                ->setParameter('motCle', "%$motCle%");

        }

        /**
         * @todo : gestion cas particuliers fitlres + unsubscribed
         */

        //Gestion pour campus
        if ($campus != null) {
            echo 'campus';
            $qb->innerJoin('s.organisateur', 'p')
                ->andWhere('p.campus = :campus')
                ->setParameter('campus', $campus);
        }


        //Gestion des filtres
        if ($dataFiltres) {
            //Sorties dont je suis l'organisateur
            if (in_array(0, $dataFiltres)) {
                echo 'data filtres 0';
                $qb->orWhere('s.organisateur = :currentUser');
                $qb->join('s.organisateur', 'o')
                    ->addSelect('o');


            }

            //Sorties dont je suis inscris
            if (in_array(1, $dataFiltres)) {
                echo 'data filtres 1';
                $qb->leftJoin('s.inscriptions', 'i')
                    ->orWhere('i.participant = :currentUser');
            }


            //Sorties dont je ne suis pas inscris
            if (in_array(2, $dataFiltres)) {
                echo 'data filtres 2';

                $listeSortiesInscrit = $this->subQueryFindUnSubsribed($currentUser);

                $qb ->addSelect('s')
                    ->where($qb->expr()->notIn('s.id', ':listeSortiesInscrit'))
                    ->setParameter('listeSortiesInscrit', $listeSortiesInscrit);
            }

            //Sorties passées
            if (in_array(3, $dataFiltres)) {
                echo 'data filtres 3';
                $qb->orWhere('s.etat = :etat6')
                    ->setParameter('etat6', Sortie::FINISHED)
                    ->join('s.etat', 'e')
                    ->addSelect('e');
            }

            if (in_array(0, $dataFiltres) || in_array(1, $dataFiltres) || in_array(3, $dataFiltres)) {
                $qb->setParameter('currentUser', $currentUser);
            }
        }


        return $qb->getQuery()->getResult();
    }

    public function subQueryFindUnSubsribed($currentUser)
    {
        $qb = $this->createQueryBuilder('s');

        //Sorties auxquelles je suis inscrit
        $qb->leftJoin('s.inscriptions', 'i')
            ->andWhere('i.participant = :currentUser')
            ->setParameter('currentUser', $currentUser);

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
        $qb2->addSelect('s2')
            ->andWhere($qb2->expr()->notIn('s2.id', ':listeSortiesInscrit'))
            ->setParameter('listeSortiesInscrit', $listeSortiesInscrit);

        return $qb2->getQuery()->getResult();








    }
}