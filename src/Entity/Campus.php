<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campus
 *
 * @ORM\Table(name="campus")
 * @ORM\Entity(repositoryClass="App\Repository\CampusRepository")
 */
class Campus
{
    /**
     * @var int
     *
     * @ORM\Column(name="no_campus", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $noCampus;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_campus", type="string", length=30, nullable=false)
     */
    private $nomCampus;

    /**
     * @return int
     */
    public function getNoCampus(): int
    {
        return $this->noCampus;
    }

    /**
     * @return string
     */
    public function getNomCampus(): string
    {
        return $this->nomCampus;
    }

    /**
     * @param string $nomCampus
     */
    public function setNomCampus(string $nomCampus): void
    {
        $this->nomCampus = $nomCampus;
    }


}
