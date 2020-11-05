<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Compte;

class CompteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 10; $i++) { 
            $compte = new Compte();
            $compte->setEmail("walid$i@yahoo.com")
                    ->setPassword(password_hash("waw$i",PASSWORD_DEFAULT));
            $manager->persist($compte);
        }

        $manager->flush();
    }
}
