<?php

namespace App\DataFixtures;

use App\Entity\AdressePostale;
use App\Entity\Trajet;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrajetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $dateNow = new DateTime();
        $date1 = $dateNow;
        
        $date2 = $dateNow;
        $date2->modify("+ 1 days");
        dump($date2);
        $dateTimeZone = new DateTimeZone('Europe/Paris');

        $donnee = array(
            array($date1,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),9),
            array($date2,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),10),
            array($date1,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),11),
            array($date2,new DateTime('10:00:00', $dateTimeZone),new DateTime('11:00:00', $dateTimeZone),7),
            array($date1,new DateTime('11:00:00', $dateTimeZone),new DateTime('12:00:00', $dateTimeZone),8),
            array($date2,new DateTime('12:00:00', $dateTimeZone),new DateTime('13:00:00', $dateTimeZone),9),
            array($date1,new DateTime('13:00:00', $dateTimeZone),new DateTime('14:00:00', $dateTimeZone),13),
            array($date2,new DateTime('15:00:00', $dateTimeZone),new DateTime('16:00:00', $dateTimeZone),12),
            array($date1,new DateTime('18:00:00', $dateTimeZone),new DateTime('19:00:00', $dateTimeZone),8),
            array($date2,new DateTime('20:00:00', $dateTimeZone),new DateTime('21:00:00', $dateTimeZone),9),
        );
        $adressePostalDepart = new AdressePostale();
        $adressePostalDepart->setRue("rue jean jaurés")->setVille("grenoble")->setNumeroRue(99);

        $adressePostalArrivee = new AdressePostale();
        $adressePostalArrivee->setRue("rue charles de gaules")->setVille("lyon")->setNumeroRue(99);

        $adressePostaleEntreprise =  $manager->getRepository(AdressePostale::class)->findAll()[0];

        $manager->persist($adressePostalDepart);
        $manager->persist($adressePostalArrivee);

        $utilisateurs = $manager->getRepository(Utilisateur::class)->findAll();
        $i=0;
        //trajets en cours
        foreach( $utilisateurs as $conducteur ) {
            $date = $donnee[$i][0];
            $heureDepart = $donnee[$i][1];
            $heureArrivee = $donnee[$i][2];
            $prix = $donnee[$i][3];

            $trajet = new Trajet();
            $trajet->setDate($date)
                    ->setConducteur($conducteur)
                    ->setHeureDepart($heureDepart)
                    ->setHeureArrivee($heureArrivee)
                    ->setAdresseDepart($adressePostalDepart)
                    ->setNbPlaces(3)
                    ->setPrix($prix)
                    ->setEtat("EN_COURS");
            if($i==1 || $i==2){
                $trajet->setAdresseArrivee($adressePostaleEntreprise);
            }else{
                $trajet->setAdresseArrivee($adressePostalArrivee);
            }
            
            $manager->persist($trajet);
            $i++;
        }

        $i=0;
        $datePasse = $dateNow->modify("- 1 days");
        $donneePassee =array(
            array($datePasse,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),9),
            array($datePasse,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),10),
            array($datePasse,new DateTime('07:00:00', $dateTimeZone),new DateTime('08:00:00', $dateTimeZone),11),
            array($datePasse,new DateTime('10:00:00', $dateTimeZone),new DateTime('11:00:00', $dateTimeZone),7),
            array($datePasse,new DateTime('11:00:00', $dateTimeZone),new DateTime('12:00:00', $dateTimeZone),8),
            array($datePasse,new DateTime('12:00:00', $dateTimeZone),new DateTime('13:00:00', $dateTimeZone),9),
            array($datePasse,new DateTime('13:00:00', $dateTimeZone),new DateTime('14:00:00', $dateTimeZone),13),
            array($datePasse,new DateTime('15:00:00', $dateTimeZone),new DateTime('16:00:00', $dateTimeZone),12),
            array($datePasse,new DateTime('18:00:00', $dateTimeZone),new DateTime('19:00:00', $dateTimeZone),8),
            array($datePasse,new DateTime('20:00:00', $dateTimeZone),new DateTime('21:00:00', $dateTimeZone),9),
        );
        //trajets passés
        foreach( $utilisateurs as $conducteur ) {
            $date = $donneePassee[$i][0];
            $heureDepart = $donneePassee[$i][1];
            $heureArrivee = $donneePassee[$i][2];
            $prix = $donneePassee[$i][3];

            $trajet = new Trajet();
            $trajet->setDate($date);
            $trajet->setConducteur($conducteur);
            $trajet->setHeureDepart($heureDepart);
            $trajet->setHeureArrivee($heureArrivee);
            $trajet->setAdresseDepart($adressePostalDepart);
            $trajet->setAdresseArrivee($adressePostalArrivee);
            $trajet->setNbPlaces(3);
            $trajet->setPrix($prix);
            if($i==5 || $i==7){
                $trajet->setEtat("ANNULER");
            }else{
                $trajet->setEtat("EFFECTUE");
            }
            $i++;

            $manager->persist($trajet);
        }

        $manager->flush();
        
    }
    public function getDependencies()
    {
        return array(
            UtilisateurFixtures::class
        );
    }
}
