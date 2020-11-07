<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201107091138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse_postale (id INT AUTO_INCREMENT NOT NULL, numero_rue INT DEFAULT NULL, rue VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, departement INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, trajet_id INT NOT NULL, destinataire_id INT NOT NULL, expediteur_id INT NOT NULL, message VARCHAR(255) NOT NULL, note DOUBLE PRECISION NOT NULL, INDEX IDX_8F91ABF0D12A823 (trajet_id), INDEX IDX_8F91ABF0A4F84F6E (destinataire_id), INDEX IDX_8F91ABF010335F61 (expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, email LONGTEXT NOT NULL, mot_de_passe LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE criteres (id INT AUTO_INCREMENT NOT NULL, fumeur TINYINT(1) DEFAULT NULL, animaux TINYINT(1) DEFAULT NULL, valise TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description (id INT AUTO_INCREMENT NOT NULL, mini_bio VARCHAR(255) DEFAULT NULL, fumeur TINYINT(1) DEFAULT NULL, bavard TINYINT(1) DEFAULT NULL, animaux TINYINT(1) DEFAULT NULL, centre_interets VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, adresse_postale_id INT NOT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D19FA60C96EEC07 (adresse_postale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE information_travail (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, horaire_debut TIME NOT NULL, horaire_fin TIME NOT NULL, INDEX IDX_8DB8DA2AA4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, demandeur_id INT NOT NULL, trajet_id INT NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_42C8495595A6EE59 (demandeur_id), INDEX IDX_42C84955D12A823 (trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT NOT NULL, adresse_depart_id INT NOT NULL, adresse_arrivee_id INT NOT NULL, date DATE NOT NULL, heure_depart TIME NOT NULL, heure_arrivee TIME NOT NULL, nb_places INT NOT NULL, prix DOUBLE PRECISION NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_2B5BA98CF16F4AC6 (conducteur_id), INDEX IDX_2B5BA98C305689D (adresse_depart_id), INDEX IDX_2B5BA98C85ED0E35 (adresse_arrivee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, description_id INT DEFAULT NULL, voiture_id INT DEFAULT NULL, criteres_id INT DEFAULT NULL, compte_id INT NOT NULL, information_travail_id INT DEFAULT NULL, nom LONGTEXT NOT NULL, prenom LONGTEXT NOT NULL, date_de_naissance DATE NOT NULL, telephone LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3D9F966B (description_id), UNIQUE INDEX UNIQ_1D1C63B3181A8BA (voiture_id), UNIQUE INDEX UNIQ_1D1C63B3A6EB9800 (criteres_id), UNIQUE INDEX UNIQ_1D1C63B3F2C56620 (compte_id), UNIQUE INDEX UNIQ_1D1C63B3EDF610A0 (information_travail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, couleur VARCHAR(255) NOT NULL, immatriculation VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF010335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60C96EEC07 FOREIGN KEY (adresse_postale_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE information_travail ADD CONSTRAINT FK_8DB8DA2AA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495595A6EE59 FOREIGN KEY (demandeur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C305689D FOREIGN KEY (adresse_depart_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C85ED0E35 FOREIGN KEY (adresse_arrivee_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D9F966B FOREIGN KEY (description_id) REFERENCES description (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A6EB9800 FOREIGN KEY (criteres_id) REFERENCES criteres (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3EDF610A0 FOREIGN KEY (information_travail_id) REFERENCES information_travail (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60C96EEC07');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C305689D');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C85ED0E35');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3F2C56620');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A6EB9800');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D9F966B');
        $this->addSql('ALTER TABLE information_travail DROP FOREIGN KEY FK_8DB8DA2AA4AEAFEA');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3EDF610A0');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D12A823');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D12A823');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A4F84F6E');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF010335F61');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495595A6EE59');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CF16F4AC6');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3181A8BA');
        $this->addSql('DROP TABLE adresse_postale');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE criteres');
        $this->addSql('DROP TABLE description');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE information_travail');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE voiture');
    }
}
