<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201120194622 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3181A8BA');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3181A8BA');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
    }
}
