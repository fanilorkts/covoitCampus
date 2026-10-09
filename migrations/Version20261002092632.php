<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002092632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0236D04AD FOREIGN KEY (id_auteur) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FD8AD0B FOREIGN KEY (id_cible) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EF2FC27C FOREIGN KEY (id_passager) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE trajets ADD prix NUMERIC(6, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA986EDF194 FOREIGN KEY (id_conducteur) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE utilisateurs ADD biographie LONGTEXT DEFAULT NULL, ADD centres_interet VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profil');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D6C1C61');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0236D04AD');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FD8AD0B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D6C1C61');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EF2FC27C');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA986EDF194');
        $this->addSql('ALTER TABLE trajets DROP prix');
        $this->addSql('ALTER TABLE utilisateurs DROP biographie, DROP centres_interet, CHANGE role role VARCHAR(255) NOT NULL');
    }
}
