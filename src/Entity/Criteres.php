<?php

namespace App\Entity;

use App\Repository\CriteresRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CriteresRepository::class)
 */
class Criteres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fumeur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $animaux;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $valise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(?bool $fumeur): self
    {
        $this->fumeur = $fumeur;

        return $this;
    }

    public function getAnimaux(): ?bool
    {
        return $this->animaux;
    }

    public function setAnimaux(?bool $animaux): self
    {
        $this->animaux = $animaux;

        return $this;
    }

    public function getValise(): ?bool
    {
        return $this->valise;
    }

    public function setValise(?bool $valise): self
    {
        $this->valise = $valise;

        return $this;
    }
}
