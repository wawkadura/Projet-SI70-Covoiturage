<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104013436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse_postale (id INT AUTO_INCREMENT NOT NULL, numero_rue INT DEFAULT NULL, rue VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, departement INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, id_trajet_id INT NOT NULL, message VARCHAR(255) NOT NULL, note DOUBLE PRECISION NOT NULL, INDEX IDX_8F91ABF08D271404 (id_trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caracteristiques (id INT AUTO_INCREMENT NOT NULL, id_description_id INT DEFAULT NULL, id_experience_id INT DEFAULT NULL, id_voiture_id INT DEFAULT NULL, id_utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_61B5DA1D30F92B6D (id_description_id), UNIQUE INDEX UNIQ_61B5DA1DF8C1DF42 (id_experience_id), UNIQUE INDEX UNIQ_61B5DA1DA40B286D (id_voiture_id), UNIQUE INDEX UNIQ_61B5DA1DC6EE5C49 (id_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE criteres (id INT AUTO_INCREMENT NOT NULL, fumeur TINYINT(1) DEFAULT NULL, animaux TINYINT(1) DEFAULT NULL, valise TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, mini_bio VARCHAR(255) DEFAULT NULL, fumeur TINYINT(1) DEFAULT NULL, bavard TINYINT(1) DEFAULT NULL, animaux TINYINT(1) DEFAULT NULL, centre_interets VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, id_adresse_postale_id INT NOT NULL, nom VARCHAR(255) NOT NULL, siret VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D19FA604A4C5EA8 (id_adresse_postale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, nb_trajet INT NOT NULL, titre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE information_travail (id INT AUTO_INCREMENT NOT NULL, id_entreprise_id INT NOT NULL, horaire_debut TIME NOT NULL, horaire_fin TIME NOT NULL, INDEX IDX_8DB8DA2A1A867E8F (id_entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, id_conducteur_id INT NOT NULL, id_adresse_depart_id INT NOT NULL, id_adresse_arrivee_id INT NOT NULL, date DATE NOT NULL, heure_depart TIME NOT NULL, heure_arrivee TIME NOT NULL, nb_places INT NOT NULL, prix DOUBLE PRECISION NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_2B5BA98C4F479BA3 (id_conducteur_id), INDEX IDX_2B5BA98CF232008A (id_adresse_depart_id), INDEX IDX_2B5BA98C6CFBC9A (id_adresse_arrivee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, couleur VARCHAR(255) NOT NULL, immatriculation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF08D271404 FOREIGN KEY (id_trajet_id) REFERENCES trajet (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1D30F92B6D FOREIGN KEY (id_description_id) REFERENCES description (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DF8C1DF42 FOREIGN KEY (id_experience_id) REFERENCES experience (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DA40B286D FOREIGN KEY (id_voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA604A4C5EA8 FOREIGN KEY (id_adresse_postale_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE information_travail ADD CONSTRAINT FK_8DB8DA2A1A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C4F479BA3 FOREIGN KEY (id_conducteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CF232008A FOREIGN KEY (id_adresse_depart_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C6CFBC9A FOREIGN KEY (id_adresse_arrivee_id) REFERENCES adresse_postale (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA604A4C5EA8');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CF232008A');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C6CFBC9A');
        $this->addSql('ALTER TABLE caracteristiques DROP FOREIGN KEY FK_61B5DA1D30F92B6D');
        $this->addSql('ALTER TABLE information_travail DROP FOREIGN KEY FK_8DB8DA2A1A867E8F');
        $this->addSql('ALTER TABLE caracteristiques DROP FOREIGN KEY FK_61B5DA1DF8C1DF42');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF08D271404');
        $this->addSql('ALTER TABLE caracteristiques DROP FOREIGN KEY FK_61B5DA1DA40B286D');
        $this->addSql('DROP TABLE adresse_postale');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE caracteristiques');
        $this->addSql('DROP TABLE criteres');
        $this->addSql('DROP TABLE description');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE information_travail');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE voiture');
    }
}
