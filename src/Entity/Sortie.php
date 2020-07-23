<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    const CREATE = '1';
    const OPEN = '2';
    const CLOSED = '3';
    const ACTIVITY = '4';
    const CANCELLED = '5';
    const FINISHED = '6';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(  min="3", minMessage="le nom de la sortie doit comporter 3 caractères au minimum",
     *                  max="30", maxMessage="le nom de la sortie doit comporter 30 caractères au maximum")
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @Assert\GreaterThan("+48 hours", message="Vous devez organiser une sortie au moins 48h à l'avance")
     * @Assert\GreaterThan(propertyPath="dateLimiteInscription",
     *     message="les inscriptions doivent se clôturer avant l'événement")
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @Assert\GreaterThan("+24 hours", message="La date limite d'inscription doit être décalée d'au moins 24h")
     * @Assert\NotBlank()
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    private $infosSortie;


    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $urlPhoto;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="sortie", cascade={"remove"})
     */
    private $inscriptions;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sortieOrganisee")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getDateHeureDebut(): ?DateTime
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(DateTime $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?DateTime
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(DateTime $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): self
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }


    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }
}
