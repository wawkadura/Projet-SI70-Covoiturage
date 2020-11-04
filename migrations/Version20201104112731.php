<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104112731 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE caracteristiques DROP FOREIGN KEY FK_61B5DA1DF8C1DF42');
        $this->addSql('DROP TABLE caracteristiques');
        $this->addSql('DROP TABLE experience');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE caracteristiques (id INT AUTO_INCREMENT NOT NULL, id_description_id INT DEFAULT NULL, id_experience_id INT DEFAULT NULL, id_voiture_id INT DEFAULT NULL, id_utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_61B5DA1DA40B286D (id_voiture_id), UNIQUE INDEX UNIQ_61B5DA1D30F92B6D (id_description_id), UNIQUE INDEX UNIQ_61B5DA1DC6EE5C49 (id_utilisateur_id), UNIQUE INDEX UNIQ_61B5DA1DF8C1DF42 (id_experience_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, nb_trajet INT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1D30F92B6D FOREIGN KEY (id_description_id) REFERENCES description (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DA40B286D FOREIGN KEY (id_voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE caracteristiques ADD CONSTRAINT FK_61B5DA1DF8C1DF42 FOREIGN KEY (id_experience_id) REFERENCES experience (id)');
    }
}
