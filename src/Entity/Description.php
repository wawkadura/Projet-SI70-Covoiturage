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
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $voyageAvecFumeur;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $voyagerAvecMusique;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $voyagerAvecAnimaux;

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

    public function getVoyageAvecFumeur(): ?bool
    {
        return $this->voyageAvecFumeur;
    }

    public function setVoyageAvecFumeur(?bool $voyageAvecFumeur): self
    {
        $this->voyageAvecFumeur = $voyageAvecFumeur;

        return $this;
    }

    public function getVoyagerAvecMusique(): ?bool
    {
        return $this->voyagerAvecMusique;
    }

    public function setVoyagerAvecMusique(?bool $voyagerAvecMusique): self
    {
        $this->voyagerAvecMusique = $voyagerAvecMusique;

        return $this;
    }

    public function getVoyagerAvecAnimaux(): ?bool
    {
        return $this->voyagerAvecAnimaux;
    }

    public function setVoyagerAvecAnimaux(?bool $voyagerAvecAnimaux): self
    {
        $this->voyagerAvecAnimaux = $voyagerAvecAnimaux;

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
