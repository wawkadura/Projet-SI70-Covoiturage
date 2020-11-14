<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères")*
     * @Assert\EqualTo(propertyPath = "confirm_motDePasse", message="Vos mot de passes ne sont pas identiques")
     */
    private $motDePasse;


    /**
     * @Assert\EqualTo(propertyPath = "motDePasse", message="Vos mot de passes ne sont pas identiques")
     */
    private $confirm_motDePasse;

 

  
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
    public function getConfirmMotDePasse()
    {
        return $this->confirm_motDePasse;
    }

    public function setConfirmMotDePasse($confirm_motdepasse): void
    {
        $this->confirm_motDePasse= $confirm_motdepasse;
    }
    public function getPassword(){ return $this->getMotDePasse();}

    public function getSalt(){}
    public function eraseCredentials(){}
    public function getUsername(){return $this->getEmail();}
    public function getRoles(){
        return ['ROLE_USER'];
    }

}
