<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fichierInscription;

    /**
     * @Assert\File(
     *      maxSize = "1024k",
     *     mimeTypes={"application/json", "text/plain"},
     *     mimeTypesMessage = "Le fichier doit être au format .json",
     *     notFoundMessage = "Le fichier n'a pas été trouvé sur le disque",
     *     uploadErrorMessage = "Erreur dans l'upload du fichier"
     * )
     */
    private $fichierInscriptionFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichierInscription(): ?string
    {
        return $this->fichierInscription;
    }

    public function setFichierInscription(?string $fichierInscription): self
    {
        $this->fichierInscription = $fichierInscription;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFichierInscriptionFile()
    {
        return $this->fichierInscriptionFile;
    }

    /**
     * @param mixed $fichierInscriptionFile
     */
    public function setFichierInscriptionFile($fichierInscriptionFile): void
    {
        $this->fichierInscriptionFile = $fichierInscriptionFile;
    }

}
