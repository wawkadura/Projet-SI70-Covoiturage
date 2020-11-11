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

        for ($i=0; $i < 10; $i++) { 
            $compte = new Compte();
            $adressePostale = new AdressePostale();
            $description = new Description();
            $description->setVoyageAvecFumeur(True)
                        ->setVoyagerAvecAnimaux(True)
                        ->setVoyagerAvecMusique(True);
            $adressePostale->setRue("rue$i")
                            ->setVille("ville$i")
                            ->setCodePostale(10000 +$i);
            $compte->setEmail("walid$i@yahoo.com")
                    ->setMotDePasse(password_hash("waw$i",PASSWORD_DEFAULT));
            
            $utilisateur = new Utilisateur();
            $utilisateur->setNom("kadura$i");
            $utilisateur->setPrenom("walid$i");
            $utilisateur->setTelephone('0612345678');
            $utilisateur->setDateDeNaissance(new \DateTime());
            $utilisateur->setDescription($description);
            $utilisateur->setAdressePostale($adressePostale);
            $utilisateur->setCompte($compte);

            $manager->persist($description);
            $manager->persist($adressePostale);
            $manager->persist($compte);
            $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
