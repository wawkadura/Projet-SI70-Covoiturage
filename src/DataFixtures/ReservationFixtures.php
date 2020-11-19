<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Trajet;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();
        $trajets = $manager->getRepository(Trajet::class)->findAll();

        for($i=0;$i<10;$i++){

            $reservation = new Reservation();
            $reservation->setDemandeur($utilisateurs[$i]);
            $reservation->setTrajet($trajets[9-$i]);
            if($i==0){
                $reservation->setEtat("ACCEPTER");
            }else{
                $reservation->setEtat("EN_ATTENTE");
            }
            $manager->persist($reservation);
        }
        
        for($i=1;$i<10;$i++){
            $reservation = new Reservation();
            $reservation->setDemandeur($utilisateurs[$i]);
            $reservation->setTrajet($trajets[0]);
            if($i%3==1){
                $reservation->setEtat("REFUSER");
            }else{
                $reservation->setEtat("EN_ATTENTE");
            }
            $manager->persist($reservation);
        }
        
        for($i=1;$i<10;$i++){
            $reservation = new Reservation();
            $reservation->setDemandeur($utilisateurs[0]);
            $reservation->setTrajet($trajets[$i]);
            if($i%3==1){
                $reservation->setEtat("REFUSER");
            }else{
                $reservation->setEtat("EN_ATTENTE");
            }
            $manager->persist($reservation);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            TrajetFixtures::class,
        );
    }
}
