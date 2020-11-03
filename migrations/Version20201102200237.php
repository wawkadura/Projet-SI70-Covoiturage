<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201102200237 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE adresse_postale');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE avis_poster');
        $this->addSql('DROP TABLE caractéristiques');
        $this->addSql('DROP TABLE criteres');
        $this->addSql('DROP TABLE description');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE info_travail');
        $this->addSql('DROP TABLE reservation_trajet');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE voiture');
        $this->addSql('ALTER TABLE compte CHANGE motdepasse motdepasse VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE nom nom LONGTEXT NOT NULL, CHANGE prenom prenom LONGTEXT NOT NULL, CHANGE datedenaissance datedenaissance DATE NOT NULL, CHANGE telephone telephone LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse_postale (id INT AUTO_INCREMENT NOT NULL, numero_rue INT DEFAULT NULL, rue TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, ville TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, id_trajet INT DEFAULT NULL, message VARCHAR(150) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, note INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE avis_poster (id_avis INT NOT NULL, id_utilisateur INT NOT NULL, INDEX id_utilisateur (id_utilisateur), PRIMARY KEY(id_avis, id_utilisateur)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE caractéristiques (id INT AUTO_INCREMENT NOT NULL, id_description INT NOT NULL, id_experience INT NOT NULL, id_voiture INT NOT NULL, id_utilisateur INT NOT NULL, INDEX id_voiture (id_voiture), INDEX id_experience (id_experience), INDEX id_utilisateur (id_utilisateur), PRIMARY KEY(id_description, id_experience, id_voiture, id_utilisateur, id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE criteres (id INT AUTO_INCREMENT NOT NULL, fumeur TINYINT(1) DEFAULT \'0\', animaux TINYINT(1) DEFAULT \'0\', valise TINYINT(1) DEFAULT \'1\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, fumer TINYINT(1) NOT NULL, bavard TINYINT(1) NOT NULL, animaux TINYINT(1) NOT NULL, mini_bio VARCHAR(100) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, centre_interets TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom_entreprise VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, siret VARCHAR(100) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, adresse_postale TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, nb_trajet INT NOT NULL, titre VARCHAR(20) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE info_travail (id INT AUTO_INCREMENT NOT NULL, horaire_debut TIME NOT NULL, horair_fin TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reservation_trajet (id_utilisateur INT NOT NULL, id_trajet INT NOT NULL, etat_reservation VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, INDEX id_trajet (id_trajet), PRIMARY KEY(id_utilisateur, id_trajet)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, date_trajet DATE NOT NULL, heur_arivee TIME NOT NULL, heure_depart TIME NOT NULL, id_adresse_depart INT NOT NULL, id_adresse_arrivee INT NOT NULL, nb_places INT NOT NULL, prix DOUBLE PRECISION NOT NULL, etat VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, INDEX id_adresse_arrivee (id_adresse_arrivee), INDEX id_adresse_depart (id_adresse_depart), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(20) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, couleur VARCHAR(10) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, plaque_immatriculation VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE compte CHANGE motdepasse motdepasse TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('ALTER TABLE utilisateur CHANGE nom nom VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE prenom prenom VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, CHANGE datedenaissance datedenaissance DATE DEFAULT NULL, CHANGE telephone telephone INT DEFAULT NULL');
    }
}
