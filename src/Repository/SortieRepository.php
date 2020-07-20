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

        //Récupération des sorties dont je ne suis pas l'organisateur et dont l'état est 'Créée'
        $listeSortiesPasOrgaEtCreee = $this->subQueryDefaultDisplay($currentUser);

        //Soustraction de la 'listeSortiesPasOrgaEtCreee' du findAll()
        $qb -> addSelect('s')
            ->orWhere($qb->expr()->notIn('s.id', ':listeSortiesPasOrgaEtCreee'))
            ->setParameter('listeSortiesPasOrgaEtCreee', $listeSortiesPasOrgaEtCreee);





        //Gestion des filtres
        if ($dataFiltres) {
            //Sorties dont je suis l'organisateur
            if (in_array(0, $dataFiltres)) {
                $qb->orWhere('s.organisateur = :currentUser');
                $qb->join('s.organisateur', 'o')
                    ->addSelect('o');


            }

            //Sorties auxquelles je suis inscrit
            if (in_array(1, $dataFiltres)) {
                $qb->leftJoin('s.inscriptions', 'i')
                    ->orWhere('i.participant = :currentUser');
            }


            //Sorties auxquelles je ne suis pas inscrit
            if (in_array(2, $dataFiltres)) {
                $listeSortiesInscrit = $this->subQueryFindUnSubsribed($currentUser);

                $qb ->addSelect('s')
                    ->orWhere($qb->expr()->notIn('s.id', ':listeSortiesInscrit'))
                    ->setParameter('listeSortiesInscrit', $listeSortiesInscrit);
            }

            //Sorties passées
            if (in_array(3, $dataFiltres)) {
                $qb->orWhere('s.etat = :etat6')
                    ->setParameter('etat6', Sortie::FINISHED)
                    ->join('s.etat', 'e')
                    ->addSelect('e');
            }

            if (in_array(0, $dataFiltres) || in_array(1, $dataFiltres) || in_array(3, $dataFiltres)) {
                $qb->setParameter('currentUser', $currentUser);
            }


        }

        //Gestion intervalle de dates
        if ($dataDateFin && $dataDateDebut)
        {
            $qb->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dataDateDebut)
                ->setParameter('dateFin', $dataDateFin);
        }

        //Gestion pour un mot clé
        if ($dataMotCle) {
            $qb->andWhere('s.nom LIKE :motCle')
                ->setParameter('motCle', "%$motCle%");
        }

        //Gestion pour campus
        if ($campus) {
            $qb->innerJoin('s.organisateur', 'p')
                ->andWhere('p.campus = :campus')
                ->setParameter('campus', $campus);
        }

        return $qb->getQuery()->getResult();
    }

    //Sous-requete nécessaire au fonctionnement du filtre sorties auxuqelles je ne suis pas inscrit
    //Récupération de la liste des sorties auxquelles je suis inscrit
    public function subQueryFindUnSubsribed($currentUser)
    {
        $qb = $this->createQueryBuilder('s');

        //Sorties auxquelles je suis inscrit
        $qb->leftJoin('s.inscriptions', 'i')
            ->andWhere('i.participant = :currentUser')
            ->setParameter('currentUser', $currentUser);

        return $qb->getQuery()->getResult();
    }

    public function subQueryDefaultDisplay($currentUser)
    {
        $qb = $this->createQueryBuilder('s');
        $qb -> andWhere('s.organisateur != :currentUser')
            -> andWhere('s.etat = :etat')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('etat', '1');

        return $qb->getQuery()->getResult();
    }
}
