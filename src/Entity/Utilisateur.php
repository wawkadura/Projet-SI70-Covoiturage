<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
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
     * @ORM\Column(type="String", lenght=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="String", lenght=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime", lenght=50)
     */
    private $datedenaissance;
    /**
     * @ORM\Column(type="int")
     */
    private $telephone;
    /**
     * @ORM\Column(type="String", lenght=100)
     */
    private $email;

    /**
     * @ORM\Column(type="String", lenght=100)
     */
    private $password;

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
    public function setNom($nom): string
    {
        $this->nom = $nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }
    public function setPrenom($prenom): string
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

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): int
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): string
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): string
    {
        $this->password = $password;
    }

}
