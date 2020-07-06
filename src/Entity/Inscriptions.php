<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscriptions
 *
 * @ORM\Table(name="inscriptions", indexes={@ORM\Index(name="inscriptions_participants_fk", columns={"participants_no_participant"})})
 * @ORM\Entity
 */
class Inscriptions
{
    /**
     * @var int
     *
     * @ORM\Column(name="sorties_no_sortie", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $sortiesNoSortie;

    /**
     * @var int
     *
     * @ORM\Column(name="participants_no_participant", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $participantsNoParticipant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime", nullable=false)
     */
    private $dateInscription;

    /**
     * @return int
     */
    public function getSortiesNoSortie(): int
    {
        return $this->sortiesNoSortie;
    }

    /**
     * @param int $sortiesNoSortie
     */
    public function setSortiesNoSortie(int $sortiesNoSortie): void
    {
        $this->sortiesNoSortie = $sortiesNoSortie;
    }

    /**
     * @return int
     */
    public function getParticipantsNoParticipant(): int
    {
        return $this->participantsNoParticipant;
    }

    /**
     * @param int $participantsNoParticipant
     */
    public function setParticipantsNoParticipant(int $participantsNoParticipant): void
    {
        $this->participantsNoParticipant = $participantsNoParticipant;
    }

    /**
     * @return \DateTime
     */
    public function getDateInscription(): \DateTime
    {
        return $this->dateInscription;
    }

    /**
     * @param \DateTime $dateInscription
     */
    public function setDateInscription(\DateTime $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }


}
