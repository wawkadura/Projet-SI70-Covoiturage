<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\Trajet;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AvisFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();
        $trajets = $manager->getRepository(Trajet::class)->findAll();
        $i=9;
        foreach($utilisateurs as $expediteur){
            $destinataire= $trajets[$i]->getConducteur();
            $i--;
            $avis = new Avis();
            $nom = $expediteur->getNom();
            $avis->setMessage(" salut je suis $nom et j'ai adorer être avec toi !")
                 ->setNote(4.5)
                 ->setDestinataire($destinataire)
                 ->setExpediteur($expediteur);
            $manager->persist($avis);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UtilisateurFixtures::class,
            TrajetFixtures::class,
            ReservationFixtures::class,
        );
    }
}
