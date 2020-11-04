<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint as Assert;


/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="l'email que vous avez indiquer est déjà utilisé!"
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
    private $motdepasse;

  
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
    public function getMotdepasse()
    {
        return $this->motdepasse;
    }

    public function setMotdepasse($motdepasse): void
    {
        $this->motdepasse= $motdepasse;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }
    public function getPassword( string $hash)
    {
        // TODO: Implement getRoles() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
