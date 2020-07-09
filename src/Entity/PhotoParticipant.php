<?php

namespace App\Entity;

use App\Repository\PhotoParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PhotoParticipantRepository::class)
 */
class PhotoParticipant
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
    private $photoNom;

    /**
     * @Assert\Image(maxSize="4M")
     */
    private $photoFile;

    /**
     * @ORM\OneToOne(targetEntity=Participant::class, mappedBy="photo", cascade={"persist", "remove"})
     */
    private $participant;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoNom(): ?string
    {
        return $this->photoNom;
    }

    public function setPhotoNom(?string $photoNom): self
    {
        $this->photoNom = $photoNom;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        // set (or unset) the owning side of the relation if necessary
        $newPhoto = null === $participant ? null : $this;
        if ($participant->getPhoto() !== $newPhoto) {
            $participant->setPhoto($newPhoto);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @param mixed $photoFile
     */
    public function setPhotoFile($photoFile): void
    {
        $this->photoFile = $photoFile;
    }

}
