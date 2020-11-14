<?php

namespace App\DataFixtures;

use App\Entity\AdressePostale;
use App\Entity\Entreprise;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EntrepriseFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 10; $i++) {
            $adressePostale = new AdressePostale();
            $adressePostale->setRue("$i RueEntreprise")
                            ->setVille("VilleEntreprise$1")
                            ->setCodePostale(99999);
            $manager->persist($adressePostale);
            $entreprise = new Entreprise();
            $entreprise->setNom("Google $i");
            $entreprise->setAdressePostale($adressePostale);
            $manager->persist($entreprise);
        }
        $manager->flush();
    }
}
