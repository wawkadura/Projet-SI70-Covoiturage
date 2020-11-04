<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrajetRepository::class)
 */
class Trajet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="trajets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idConducteur;

    /**
     * @ORM\Column(type="time")
     */
    private $heureDepart;

    /**
     * @ORM\Column(type="time")
     */
    private $heureArrivee;

    /**
     * @ORM\ManyToOne(targetEntity=adressePostale::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $idAdresseDepart;

    /**
     * @ORM\ManyToOne(targetEntity=adressePostale::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $idAdresseArrivee;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPlaces;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdConducteur(): ?Utilisateur
    {
        return $this->idConducteur;
    }

    public function setIdConducteur(?Utilisateur $idConducteur): self
    {
        $this->idConducteur = $idConducteur;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): self
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface $heureArrivee): self
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getIdAdresseDepart(): ?adressePostale
    {
        return $this->idAdresseDepart;
    }

    public function setIdAdresseDepart(?adressePostale $idAdresseDepart): self
    {
        $this->idAdresseDepart = $idAdresseDepart;

        return $this;
    }

    public function getIdAdresseArrivee(): ?adressePostale
    {
        return $this->idAdresseArrivee;
    }

    public function setIdAdresseArrivee(?adressePostale $idAdresseArrivee): self
    {
        $this->idAdresseArrivee = $idAdresseArrivee;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): self
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
