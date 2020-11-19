<?php

namespace App\DataFixtures;

use App\Entity\AdressePostale;
use App\Entity\Compte;
use App\Entity\Description;
use App\Entity\Entreprise;
use App\Entity\InformationTravail;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Repository\CompteRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $dateTimeZone = new DateTimeZone('Europe/Paris');
        $dateTimeHoraireDebut = new DateTime('08:00:00', $dateTimeZone);
        $dateTimeHoraireFin = new DateTime('17:00:00', $dateTimeZone);

        $donnees = array(
            array('walid','kadura', new DateTime('1999-04-24'),$dateTimeHoraireDebut,$dateTimeHoraireFin),
            array('lucas','modric',new DateTime('2000-04-24'),new DateTime('07:00:00', $dateTimeZone),new DateTime('16:00:00', $dateTimeZone)),
            array('benjamin','pavard',new DateTime('1995-04-24'),new DateTime('09:00:00', $dateTimeZone),new DateTime('18:00:00', $dateTimeZone)),
            array('muftah','lionel',new DateTime('1994-04-12'),new DateTime('09:30:00', $dateTimeZone),new DateTime('18:30:00', $dateTimeZone)),
            array('thomas','Du sud',new DateTime('1998-04-03'),$dateTimeHoraireDebut,new DateTime('16:00:00', $dateTimeZone)),
            array('annie','bertran',new DateTime('1995-04-24'),$dateTimeHoraireDebut,$dateTimeHoraireFin),
            array('elise','koman',new DateTime('1990-06-05'),$dateTimeHoraireDebut,$dateTimeHoraireFin),
            array('melodie','tse',new DateTime('1999-04-10'),$dateTimeHoraireDebut,$dateTimeHoraireFin),
            array('john','wiliam',new DateTime('1999-04-15'),$dateTimeHoraireDebut,$dateTimeHoraireFin),
            array('charlotte','kawafi',new DateTime('2001-07-23'),$dateTimeHoraireDebut,$dateTimeHoraireFin)
        );
        $adressePostale = new AdressePostale();
        $adressePostale->setNumeroRue(99)
                        ->setRue("RueDomicile")
                        ->setVille("VilleDomicile");
        $adressePostaleSpecial = new AdressePostale();
        $adressePostaleSpecial->setNumeroRue(99)
                        ->setRue("rue charles de gaules")
                        ->setVille("Belfort");
                        
        $manager->persist($adressePostale);
        $entreprises = $manager->getRepository(Entreprise::class)->findAll();

        for ($i=0; $i < 10; $i++) {
            $nom = $donnees[$i][1];
            $prenom= $donnees[$i][0];
            $dateNaissance = $donnees[$i][2];
            $horaireDebut = $donnees[$i][3];
            $horaireFin = $donnees[$i][4];

            $description = new Description();
            if($i%3 ==0){
                $description->setVoyagerAvecFumeur(true)
                            ->setVoyagerAvecAnimaux(false)
                            ->setVoyagerAvecMusique(true);
            }elseif($i%3==1){
                $description->setVoyagerAvecFumeur(false)
                            ->setVoyagerAvecAnimaux(false)
                            ->setVoyagerAvecMusique(true);
            }else{
                $description->setVoyagerAvecFumeur(true)
                            ->setVoyagerAvecAnimaux(true)
                            ->setVoyagerAvecMusique(true);
            }

            $manager->persist($description);

            $compte = new Compte();
            $compte->setEmail("$prenom@yahoo.com")
                    ->setMotDePasse(password_hash("$prenom",PASSWORD_DEFAULT));
            $manager->persist($compte);

            $voiture = new Voiture();
            $voiture->setMarque("Renault")
                    ->setCouleur("Rouge")
                    ->setImmatriculation("AB-00$i-CD")
                    ->setModel("Clio");
            $manager->persist($voiture);

            $informationsTravail= new InformationTravail();
            $informationsTravail->setHoraireDebut($horaireDebut)
                                ->setHoraireFin($horaireFin);
                                
            if($i==0 && $i ==1 && $i==2){
                $informationsTravail->setEntreprise($entreprises[0]);
            }
            else{
                $informationsTravail->setEntreprise($entreprises[$i%4]);
            }
            $manager->persist($informationsTravail);

            $utilisateur = new Utilisateur();
            $utilisateur->setNom("$nom");
            $utilisateur->setPrenom("$prenom");
            $utilisateur->setTelephone('0612345678');
            $utilisateur->setDateDeNaissance($dateNaissance);
            $utilisateur->setDescription($description);
            $utilisateur->setAdressePostale($adressePostale);
            $utilisateur->setVoiture($voiture);
            $utilisateur->setCompte($compte);
            $utilisateur->setInformationTravail($informationsTravail);
            $manager->persist($utilisateur);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            EntrepriseFixtures::class,
        );
    }
}
