<?php

namespace App\DataFixtures;

use App\Entity\AdressePostale;
use App\Entity\Compte;
use App\Entity\Description;
use App\Entity\Utilisateur;
use App\Repository\CompteRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $adressePostale = new AdressePostale();
        $adressePostale->setRue("RandomRue")
                        ->setVille("RandomVille")
                        ->setCodePostale(10000);
        
        for ($i=0; $i < 10; $i++) { 

            $description = new Description();
            $description->setVoyagerAvecFumeur(true)
                        ->setVoyagerAvecAnimaux(true)
                        ->setVoyagerAvecMusique(true);
            $manager->persist($description);

            $compte = new Compte();
            $compte->setEmail("walid$i@yahoo.com")
                    ->setMotDePasse(password_hash("wawverycool",PASSWORD_DEFAULT));
            $manager->persist($compte);
            
            $utilisateur = new Utilisateur();
            $utilisateur->setNom("RANDOM$i");
            $utilisateur->setPrenom("Number$i");
            $utilisateur->setTelephone('0612345678');
            $utilisateur->setDateDeNaissance(new \DateTime());
            $utilisateur->setDescription($description);
            $utilisateur->setAdressePostale($adressePostale);
            $utilisateur->setCompte($compte);
            $manager->persist($utilisateur);
            
        }
        $manager->persist($adressePostale);
        $manager->flush();
    }
}
