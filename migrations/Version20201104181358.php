<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104181358 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD description_id INT DEFAULT NULL, ADD voiture_id INT DEFAULT NULL, ADD criteres_id INT DEFAULT NULL, ADD information_travail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D9F966B FOREIGN KEY (description_id) REFERENCES description (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A6EB9800 FOREIGN KEY (criteres_id) REFERENCES criteres (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3EDF610A0 FOREIGN KEY (information_travail_id) REFERENCES information_travail (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3D9F966B ON utilisateur (description_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3181A8BA ON utilisateur (voiture_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3A6EB9800 ON utilisateur (criteres_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3EDF610A0 ON utilisateur (information_travail_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D9F966B');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3181A8BA');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A6EB9800');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3EDF610A0');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3D9F966B ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3181A8BA ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3A6EB9800 ON utilisateur');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3EDF610A0 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP description_id, DROP voiture_id, DROP criteres_id, DROP information_travail_id');
    }
}
