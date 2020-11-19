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
        $donnees = array(
            array('Google',10,'silicone valley', 'grenoble'),
            array('Amazon',54,'silicone valley', 'lyon'),
            array('Intel',13,'silicone valley', 'paris'),
            array('Facebook',18,'silicone valley', 'belfort'),
            array('Apple',20,'silicone valley', 'lille')
        );
        for ($i=0; $i < 5; $i++) {
            $nomEntreprise = $donnees[$i][0];
            $numRueEntreprise = $donnees[$i][1];
            $rueEntreprise = $donnees[$i][2];
            $villeEntreprise = $donnees[$i][3];
            $adressePostale = new AdressePostale();

            $adressePostale->setRue($rueEntreprise)
                            ->setVille($villeEntreprise)
                            ->setNumeroRue($numRueEntreprise);
            $manager->persist($adressePostale);

            $entreprise = new Entreprise();
            $entreprise->setNom($nomEntreprise);
            $entreprise->setAdressePostale($adressePostale);

            $manager->persist($entreprise);
        }
        $manager->flush();
    }
}
