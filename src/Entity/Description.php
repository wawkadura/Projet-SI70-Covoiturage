<?php

namespace App\Entity;

use App\Repository\DescriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DescriptionRepository::class)
 */
class Description
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $miniBio;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fumeur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bavard;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $animaux;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $centreInterets;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMiniBio(): ?string
    {
        return $this->miniBio;
    }

    public function setMiniBio(?string $miniBio): self
    {
        $this->miniBio = $miniBio;

        return $this;
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

    public function getBavard(): ?bool
    {
        return $this->bavard;
    }

    public function setBavard(?bool $bavard): self
    {
        $this->bavard = $bavard;

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

    public function getCentreInterets(): ?string
    {
        return $this->centreInterets;
    }

    public function setCentreInterets(?string $centreInterets): self
    {
        $this->centreInterets = $centreInterets;

        return $this;
    }
}
