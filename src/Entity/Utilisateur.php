<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *   Utilisateur
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDeNaissance;

    /**
     * @ORM\Column(type="text")
     */
    private $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=AdressePostale::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $adressePostale;
    
    /**
     * @ORM\OneToMany(targetEntity=Trajet::class, mappedBy="conducteur")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trajetsProposer;
    
    /**
     * @ORM\OneToOne(targetEntity=Description::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Voiture::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $voiture;

    /**
     * @ORM\OneToOne(targetEntity=Compte::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=false) 
     */
    private $compte;
    
    /**
     * @ORM\OneToOne(targetEntity=InformationTravail::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $informationTravail;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $reservations;

    public function __construct()
    {
        $this->trajetsProposer = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->avisRecu = new ArrayCollection();
        $this->avisPoste = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getNom()
    {
        return $this->nom;
    }
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }
    public function setPrenom($prenom):void
    {
        $this->prenom = $prenom;
    }

    public function getDateDeNaissance()
    {
        return $this->dateDeNaissance;
    }
    public function setDateDeNaissance($dateDeNaissance): void
    {
        $this->dateDeNaissance = $dateDeNaissance;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getAdressePostale()
    {
        return $this->adressePostale;
    }

    public function setAdressePostale($adressePostale): void
    {
        $this->adressePostale = $adressePostale;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getVoiture()
    {
        return $this->voiture;
    }

    public function setVoiture($voiture): void
    {
        $this->voiture = $voiture;
    }

    public function getCompte()
    {
        return $this->compte;
    }

    public function setCompte($compte): void
    {
        $this->compte = $compte;
    }

    public function getInformationTravail()
    {
        return $this->informationTravail;
    }

    public function setInformationTravail($informationTravail): void
    {
        $this->informationTravail = $informationTravail;
    }
    /**
     * @return Collection|Trajet[]
     */
    public function getTrajetsProposer(): Collection
    {
        return $this->trajetsProposer;
    }

    public function addTrajet(Trajet $trajet): self
    {
        if (!$this->trajetsProposer->contains($trajet)) {
            $this->trajetsProposer[] = $trajet;
            $trajet->setConducteur($this);
        }

        return $this;
    }

    public function removeTrajet(Trajet $trajet): self
    {
        if ($this->trajetsProposer->removeElement($trajet)) {
            // set the owning side to null (unless already changed)
            if ($trajet->getConducteur() === $this) {
                $trajet->setConducteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setDemandeur($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getDemandeur() === $this) {
                $reservation->setDemandeur(null);
            }
        }

        return $this;
    }

}
