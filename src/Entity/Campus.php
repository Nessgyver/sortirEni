<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="campus")
     */
    private $stagiaire;

    public function __construct()
    {
        $this->stagiaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getStagiaire(): Collection
    {
        return $this->stagiaire;
    }

    public function addStagiaire(Participant $stagiaire): self
    {
        if (!$this->stagiaire->contains($stagiaire)) {
            $this->stagiaire[] = $stagiaire;
            $stagiaire->setCampus($this);
        }

        return $this;
    }

    public function removeStagiaire(Participant $stagiaire): self
    {
        if ($this->stagiaire->contains($stagiaire)) {
            $this->stagiaire->removeElement($stagiaire);
            // set the owning side to null (unless already changed)
            if ($stagiaire->getCampus() === $this) {
                $stagiaire->setCampus(null);
            }
        }

        return $this;
    }
}
