<?php

namespace App\DataFixtures;

use App\Entity\AdressePostale;
use App\Entity\Trajet;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrajetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        for ($i=1; $i <= 10; $i++) {
            
            $adressePostalDepart = new AdressePostale();
            $adressePostalDepart->setRue("$i rue jean jaurés");
            $adressePostalDepart->setVille("Grenoble");
            $adressePostalDepart->setCodePostale(38000);

            $adressePostalArrivee = new AdressePostale();
            $adressePostalArrivee->setRue("$i rue charles de gaules");
            $adressePostalArrivee->setVille("Lyon");
            $adressePostalArrivee->setCodePostale(69000);

            $conducteur = $manager->getRepository(Utilisateur::class)
                                ->findBy(array("id"=>$i))[0];
            $trajet = new Trajet();
            $trajet->setDate(new \DateTime());
            $trajet->setConducteur($conducteur);
            $trajet->setHeureDepart(new \DateTime());
            $trajet->setHeureArrivee(new \DateTime());
            $trajet->setAdresseDepart($adressePostalDepart);
            $trajet->setAdresseArrivee($adressePostalArrivee);
            $trajet->setNbPlaces($i);
            $trajet->setPrix(6+$i);
            $trajet->setEtat("En cours");
            
            $manager->persist($adressePostalDepart);
            $manager->persist($adressePostalArrivee);
            $manager->persist($trajet);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UtilisateurFixtures::class,
        );
    }
}
