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
     *      maxSize = "1024k"
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

    public function csvstring_to_array($string, $separatorChar = ',', $enclosureChar = '"', $newlineChar = "\n") {
        $array = array();
        $size = strlen($string);
        $columnIndex = 0;
        $rowIndex = 0;
        $fieldValue="";
        $isEnclosured = false;
        for($i=0; $i<$size;$i++) {

            $char = $string{$i};
            $addChar = "";

            if($isEnclosured) {
                if($char==$enclosureChar) {

                    if($i+1<$size && $string{$i+1}==$enclosureChar){
                        // escaped char
                        $addChar=$char;
                        $i++; // dont check next char
                    }else{
                        $isEnclosured = false;
                    }
                }else {
                    $addChar=$char;
                }
            }else {
                if($char==$enclosureChar) {
                    $isEnclosured = true;
                }else {

                    if($char==$separatorChar) {

                        $array[$rowIndex][$columnIndex] = $fieldValue;
                        $fieldValue="";

                        $columnIndex++;
                    }elseif($char==$newlineChar) {
                        echo $char;
                        $array[$rowIndex][$columnIndex] = $fieldValue;
                        $fieldValue="";
                        $columnIndex=0;
                        $rowIndex++;
                    }else {
                        $addChar=$char;
                    }
                }
            }
            if($addChar!=""){
                $fieldValue.=$addChar;

            }
        }

        if($fieldValue) { // save last field
            $array[$rowIndex][$columnIndex] = $fieldValue;
        }
        return $array;
    }
}
