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
        //Récupération de l'utilisateur courant
        $currentUser = $this->security->getUser();

        //Récupération des données du formulaire
        $campus = $data['campus'];
        $dataFiltres = $data['filtres'];
        $dataBrutesMotsCles = $data['motCle'];
        $dataMotsCles = $this->multiExplode(array(" ", ",", ".",":", ", "), $dataBrutesMotsCles);
        $dataDateDebut = $data['dateDebut'];
        $dataDateFin = $data['dateFin'];


        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.inscriptions', 'i')
            ->addSelect('i')
            ->innerJoin('s.organisateur', 'p')
            ->addSelect('p')
            ->innerJoin('s.etat', 'e')
            ->addSelect('e');

        //Gestion des filtres
        if ($dataFiltres) {
            //Sorties dont je suis l'organisateur
            if (in_array(0, $dataFiltres))
            {
                $qb->andWhere('s.organisateur = :currentUser');
            }

            //Sorties auxquelles je suis inscrit
            if (in_array(1, $dataFiltres))
            {
                $qb->andWhere('i.participant = :currentUser');
            }

            //Sorties auxquelles je ne suis pas inscrit
            if (in_array(2, $dataFiltres))
            {
                $listeSortiesInscrit = $this->getListeSortiesInscrit($currentUser);
                $qb ->addSelect('s')
                    ->andWhere($qb->expr()->notIn('s.id', ':listeSortiesInscrit'))
                    ->setParameter('listeSortiesInscrit', $listeSortiesInscrit);
            }

            //Sorties passées
            if (in_array(3, $dataFiltres))
            {
                $qb->andWhere('s.etat = :etat6')
                    ->setParameter('etat6', Sortie::FINISHED);
            }

            if (in_array(0, $dataFiltres) || in_array(1, $dataFiltres)) {
                $qb->setParameter('currentUser', $currentUser);
            }
        } else {
            //Récupération des sorties dont je ne suis pas l'organisateur et dont l'état est 'Créée'
            $listeSortiesPasOrgaEtCreee = $this->getListeSortiesOrganisateurEtCreee($currentUser);

            //Soustraction de la 'listeSortiesPasOrgaEtCreee' du findAll()
            $qb -> addSelect('s')
                ->orWhere($qb->expr()->notIn('s.id', ':listeSortiesPasOrgaEtCreee'))
                ->setParameter('listeSortiesPasOrgaEtCreee', $listeSortiesPasOrgaEtCreee);
        }

        //Gestion intervalle de dates
        if ($dataDateFin && $dataDateDebut)
        {
            $qb->andWhere('s.dateHeureDebut <= :dateFin')
                ->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dataDateDebut)
                ->setParameter('dateFin', $dataDateFin);
        }

        //Gestion pour un ou plusieur(s) mot(s) clé(s)
        if ($dataMotsCles[0] != "") {
            foreach ($dataMotsCles as $m => $motCle)
            {
                $qb->andWhere("s.nom LIKE :motCle$m");
                $qb->setParameter("motCle$m", "%$motCle%");
            }
        }

        //Gestion pour campus
        if ($campus) {
            $qb->andWhere('p.campus = :campus')
                ->setParameter('campus', $campus);
        }

        //Tri par défault par état (croissant) puis par date (croissant)
        $qb->addOrderBy('s.etat', 'ASC')
            ->addOrderBy('s.dateHeureDebut', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fonction retournant une liste de sorties pour lesquelles je suis inscrit
     * @param $currentUser
     * @return int|mixed|string
     */

    public function getListeSortiesInscrit($currentUser)
    {
        $qb = $this->createQueryBuilder('s');

        //Sorties auxquelles je suis inscrit
        $qb->leftJoin('s.inscriptions', 'i')
            ->andWhere('i.participant = :currentUser')
            ->setParameter('currentUser', $currentUser);

        return $qb->getQuery()->getResult();
    }

    /**
     * Fonction retournant une liste de sorties pour lesquelles je suis organisateur ET l'état de la sortie est "Créée"(1 en BDD).
     *
     * @param $currentUser
     * @return int|mixed|string
     */
    public function getListeSortiesOrganisateurEtCreee($currentUser)
    {
        $qb = $this->createQueryBuilder('s');
        $qb -> andWhere('s.organisateur != :currentUser')
            -> andWhere('s.etat = :etat')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('etat', '1');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fonction permettant de faire un explode() avec plusieurs délimiteurs
     *
     * @param $delimiters
     * @param $string
     * @return false|string[]
     */
    public function multiExplode ($delimiters,$string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);

        return  $launch;
    }

}
