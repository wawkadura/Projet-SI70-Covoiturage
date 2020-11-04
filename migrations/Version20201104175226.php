<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104175226 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, demandeur_id INT NOT NULL, trajet_id INT NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_42C8495595A6EE59 (demandeur_id), INDEX IDX_42C84955D12A823 (trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495595A6EE59 FOREIGN KEY (demandeur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id)');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF08D271404');
        $this->addSql('DROP INDEX IDX_8F91ABF08D271404 ON avis');
        $this->addSql('ALTER TABLE avis ADD destinataire_id INT NOT NULL, ADD expediteur_id INT NOT NULL, CHANGE id_trajet_id trajet_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF010335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0D12A823 ON avis (trajet_id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0A4F84F6E ON avis (destinataire_id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF010335F61 ON avis (expediteur_id)');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA604A4C5EA8');
        $this->addSql('DROP INDEX UNIQ_D19FA604A4C5EA8 ON entreprise');
        $this->addSql('ALTER TABLE entreprise CHANGE id_adresse_postale_id adresse_postale_id INT NOT NULL');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60C96EEC07 FOREIGN KEY (adresse_postale_id) REFERENCES adresse_postale (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60C96EEC07 ON entreprise (adresse_postale_id)');
        $this->addSql('ALTER TABLE information_travail DROP FOREIGN KEY FK_8DB8DA2A1A867E8F');
        $this->addSql('DROP INDEX IDX_8DB8DA2A1A867E8F ON information_travail');
        $this->addSql('ALTER TABLE information_travail CHANGE id_entreprise_id entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE information_travail ADD CONSTRAINT FK_8DB8DA2AA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_8DB8DA2AA4AEAFEA ON information_travail (entreprise_id)');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C4F479BA3');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C6CFBC9A');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CF232008A');
        $this->addSql('DROP INDEX IDX_2B5BA98C6CFBC9A ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98C4F479BA3 ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98CF232008A ON trajet');
        $this->addSql('ALTER TABLE trajet ADD conducteur_id INT NOT NULL, ADD adresse_depart_id INT NOT NULL, ADD adresse_arrivee_id INT NOT NULL, DROP id_conducteur_id, DROP id_adresse_depart_id, DROP id_adresse_arrivee_id');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C305689D FOREIGN KEY (adresse_depart_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C85ED0E35 FOREIGN KEY (adresse_arrivee_id) REFERENCES adresse_postale (id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98CF16F4AC6 ON trajet (conducteur_id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C305689D ON trajet (adresse_depart_id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C85ED0E35 ON trajet (adresse_arrivee_id)');
        $this->addSql('ALTER TABLE utilisateur ADD compte_id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3F2C56620 ON utilisateur (compte_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D12A823');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A4F84F6E');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF010335F61');
        $this->addSql('DROP INDEX IDX_8F91ABF0D12A823 ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0A4F84F6E ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF010335F61 ON avis');
        $this->addSql('ALTER TABLE avis ADD id_trajet_id INT NOT NULL, DROP trajet_id, DROP destinataire_id, DROP expediteur_id');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF08D271404 FOREIGN KEY (id_trajet_id) REFERENCES trajet (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF08D271404 ON avis (id_trajet_id)');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60C96EEC07');
        $this->addSql('DROP INDEX UNIQ_D19FA60C96EEC07 ON entreprise');
        $this->addSql('ALTER TABLE entreprise CHANGE adresse_postale_id id_adresse_postale_id INT NOT NULL');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA604A4C5EA8 FOREIGN KEY (id_adresse_postale_id) REFERENCES adresse_postale (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA604A4C5EA8 ON entreprise (id_adresse_postale_id)');
        $this->addSql('ALTER TABLE information_travail DROP FOREIGN KEY FK_8DB8DA2AA4AEAFEA');
        $this->addSql('DROP INDEX IDX_8DB8DA2AA4AEAFEA ON information_travail');
        $this->addSql('ALTER TABLE information_travail CHANGE entreprise_id id_entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE information_travail ADD CONSTRAINT FK_8DB8DA2A1A867E8F FOREIGN KEY (id_entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_8DB8DA2A1A867E8F ON information_travail (id_entreprise_id)');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CF16F4AC6');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C305689D');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C85ED0E35');
        $this->addSql('DROP INDEX IDX_2B5BA98CF16F4AC6 ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98C305689D ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98C85ED0E35 ON trajet');
        $this->addSql('ALTER TABLE trajet ADD id_conducteur_id INT NOT NULL, ADD id_adresse_depart_id INT NOT NULL, ADD id_adresse_arrivee_id INT NOT NULL, DROP conducteur_id, DROP adresse_depart_id, DROP adresse_arrivee_id');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C4F479BA3 FOREIGN KEY (id_conducteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C6CFBC9A FOREIGN KEY (id_adresse_arrivee_id) REFERENCES adresse_postale (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CF232008A FOREIGN KEY (id_adresse_depart_id) REFERENCES adresse_postale (id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C6CFBC9A ON trajet (id_adresse_arrivee_id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C4F479BA3 ON trajet (id_conducteur_id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98CF232008A ON trajet (id_adresse_depart_id)');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3F2C56620');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3F2C56620 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP compte_id');
    }
}
