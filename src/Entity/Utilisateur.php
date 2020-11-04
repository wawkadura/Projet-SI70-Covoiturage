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
    private $datedenaissance;

    /**
     * @ORM\Column(type="text")
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Trajet::class, mappedBy="conducteur")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trajetsProposer;

    /**
     * @ORM\OneToOne(targetEntity=Description::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity=Voiture::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $voiture;

    /**
     * @ORM\OneToOne(targetEntity=Criteres::class, mappedBy="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $criteres;

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

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="id", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $avisRecu;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="id", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $avisPoste;


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

    public function getDatedenaissance()
    {
        return $this->datedenaissance;
    }
    public function setDatedenaissance($datedenaissance): void
    {
        $this->datedenaissance = $datedenaissance;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
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

    public function getCriteres()
    {
        return $this->criteres;
    }

    public function setCriteres($criteres): void
    {
        $this->criteres = $criteres;
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

    /**
     * @return Collection|Avis[]
     */
    public function getAvisRecu(): Collection
    {
        return $this->avisRecu;
    }

    public function addAvisRecu(Avis $avisRecu): self
    {
        if (!$this->avisRecu->contains($avisRecu)) {
            $this->avisRecu[] = $avisRecu;
            $avisRecu->setDestinataire($this);
        }

        return $this;
    }

    public function removeAvisRecu(Avis $avisRecu): self
    {
        if ($this->avisRecu->removeElement($avisRecu)) {
            // set the owning side to null (unless already changed)
            if ($avisRecu->getDestinataire() === $this) {
                $avisRecu->setDestinataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvisPoste(): Collection
    {
        return $this->avisPoste;
    }

    public function addAvisPoste(Avis $avisPoste): self
    {
        if (!$this->avisPoste->contains($avisPoste)) {
            $this->avisPoste[] = $avisPoste;
            $avisPoste->setExpediteur($this);
        }

        return $this;
    }

    public function removeAvisPoste(Avis $avisPoste): self
    {
        if ($this->avisPoste->removeElement($avisPoste)) {
            // set the owning side to null (unless already changed)
            if ($avisPoste->getExpediteur() === $this) {
                $avisPoste->setExpediteur(null);
            }
        }

        return $this;
    }

}
