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
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $adressePostale = new AdressePostale();
        $adressePostale->setRue("RandomRue")
                        ->setVille("RandomVille")
                        ->setCodePostale(10000);
        $manager->persist($adressePostale);
        $entreprises = $manager->getRepository(Entreprise::class)->findAll();

        for ($i=0; $i < 10; $i++) {

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
            $compte->setEmail("random$i@yahoo.com")
                    ->setMotDePasse(password_hash("$i",PASSWORD_DEFAULT));
            $manager->persist($compte);

            $voiture = new Voiture();
            $voiture->setMarque("Renault")
                    ->setCouleur("Rouge")
                    ->setImmatriculation("AB-00$i-CD")
                    ->setModel("Clio");
            $manager->persist($voiture);

            $informationsTravail= new InformationTravail();
            $informationsTravail->setHoraireDebut(new \DateTime())
                                ->setHoraireFin(new \DateTime());
                                
            if($i==1 && $i ==2 && $i==3){
                $informationsTravail->setEntreprise($entreprises[0]);
            }
            else{
                $informationsTravail->setEntreprise($entreprises[$i]);
            }
            $manager->persist($informationsTravail);

            $utilisateur = new Utilisateur();
            $utilisateur->setNom("RANDOM$i");
            $utilisateur->setPrenom("Number$i");
            $utilisateur->setTelephone('0612345678');
            $utilisateur->setDateDeNaissance(new \DateTime());
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
