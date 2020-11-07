<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint as Assert;
use App\Entity\CompteType;


/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="l'email est déjà utilisé!"
 * )
 */
class Compte implements UserInterface
{
    /**
     *  Compte
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $motDePasse;

  
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    public function setMotDePasse($motdepasse): void
    {
        $this->motDePasse= $motdepasse;
    }
    public function getPassword(){ return $this->getMotDePasse();}

    public function getSalt(){}
    public function eraseCredentials(){}
    public function getUsername(){return "User $this->id";}
    public function getRoles(){
        return ['ROLE_USER'];
    }

}
