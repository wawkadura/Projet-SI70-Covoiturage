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
        $adressePostalDepart = new AdressePostale();
        $adressePostalDepart->setRue("10 rue jean jaurés");
        $adressePostalDepart->setVille("Grenoble");
        $adressePostalDepart->setCodePostale(38000);

        $adressePostalArrivee = new AdressePostale();
        $adressePostalArrivee->setRue("10 rue charles de gaules");
        $adressePostalArrivee->setVille("Lyon");
        $adressePostalArrivee->setCodePostale(69000);
        
        $manager->persist($adressePostalDepart);
        $manager->persist($adressePostalArrivee);

        for ($i=1; $i <= 10; $i++) {
            

            $conducteur = $manager->getRepository(Utilisateur::class)
                                ->findBy(array("id"=>$i))[0];
            $trajet = new Trajet();
            $trajet->setDate(new \DateTime());
            $trajet->setConducteur($conducteur);
            $trajet->setHeureDepart(new \DateTime());
            $trajet->setHeureArrivee(new \DateTime());
            $trajet->setAdresseDepart($adressePostalDepart);
            $trajet->setAdresseArrivee($adressePostalArrivee);
            $trajet->setNbPlaces(3);
            $trajet->setPrix(6+$i);
            $trajet->setEtat("EN COURS");
            

            $manager->persist($trajet);
        }
        for ($i=1; $i <= 10; $i++) {
            

            $conducteur = $manager->getRepository(Utilisateur::class)
                                ->findBy(array("id"=>$i))[0];
            $trajet = new Trajet();
            $trajet->setDate(new \DateTime());
            $trajet->setConducteur($conducteur);
            $trajet->setHeureDepart(new \DateTime());
            $trajet->setHeureArrivee(new \DateTime());
            $trajet->setAdresseDepart($adressePostalDepart);
            $trajet->setAdresseArrivee($adressePostalArrivee);
            $trajet->setNbPlaces(3);
            $trajet->setPrix(6+$i);
            if($i==5 || $i==7){
                $trajet->setEtat("ANNULER");
            }else{
                $trajet->setEtat("TERMINE");
            }
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
