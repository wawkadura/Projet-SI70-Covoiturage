<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
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

}
