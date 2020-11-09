<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Repository\CompteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i=0; $i < 10; $i++) { 
            $compte = new Compte();
            $compte->setEmail("walid$i@yahoo.com")
                    ->setMotDePasse(password_hash("waw$i",PASSWORD_DEFAULT));
            
            $utilisateur = new Utilisateur();
            $utilisateur->setNom("Smith$i");
            $utilisateur->setPrenom("walid$i");
            $utilisateur->setTelephone('0612345678');
            $utilisateur->setDateDeNaissance(new \DateTime());
            $utilisateur->setCompte($compte);

            $manager->persist($compte);
            $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
