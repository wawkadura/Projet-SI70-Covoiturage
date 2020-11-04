<?php

namespace App\Entity;

use App\Repository\CaracteristiquesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaracteristiquesRepository::class)
 */
class Caracteristiques
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Description::class, cascade={"persist", "remove"})
     */
    private $idDescription;

    /**
     * @ORM\OneToOne(targetEntity=Experience::class, cascade={"persist", "remove"})
     */
    private $idExperience;

    /**
     * @ORM\OneToOne(targetEntity=Voiture::class, cascade={"persist", "remove"})
     */
    private $idVoiture;

    /**
     * @ORM\OneToOne(targetEntity=Utilisateur::class, cascade={"persist", "remove"})
     */
    private $idUtilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdDescription(): ?Description
    {
        return $this->idDescription;
    }

    public function setIdDescription(?Description $idDescription): self
    {
        $this->idDescription = $idDescription;

        return $this;
    }

    public function getIdExperience(): ?Experience
    {
        return $this->idExperience;
    }

    public function setIdExperience(?Experience $idExperience): self
    {
        $this->idExperience = $idExperience;

        return $this;
    }

    public function getIdVoiture(): ?Voiture
    {
        return $this->idVoiture;
    }

    public function setIdVoiture(?Voiture $idVoiture): self
    {
        $this->idVoiture = $idVoiture;

        return $this;
    }

    public function getIdUtilisateur(): ?utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?utilisateur $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }
}
