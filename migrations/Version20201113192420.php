<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201113192420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP INDEX UNIQ_1D1C63B3C96EEC07, ADD INDEX IDX_1D1C63B3C96EEC07 (adresse_postale_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP INDEX IDX_1D1C63B3C96EEC07, ADD UNIQUE INDEX UNIQ_1D1C63B3C96EEC07 (adresse_postale_id)');
    }
}
