<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260929234602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE id_trajet id_trajet INT DEFAULT NULL, CHANGE id_auteur id_auteur INT DEFAULT NULL, CHANGE id_cible id_cible INT DEFAULT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0236D04AD FOREIGN KEY (id_auteur) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FD8AD0B FOREIGN KEY (id_cible) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0D6C1C61 ON avis (id_trajet)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0236D04AD ON avis (id_auteur)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0FD8AD0B ON avis (id_cible)');
        $this->addSql('ALTER TABLE reservation CHANGE id_trajet id_trajet INT DEFAULT NULL, CHANGE id_passager id_passager INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EF2FC27C FOREIGN KEY (id_passager) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_42C84955D6C1C61 ON reservation (id_trajet)');
        $this->addSql('CREATE INDEX IDX_42C84955EF2FC27C ON reservation (id_passager)');
        $this->addSql('ALTER TABLE trajets CHANGE places_restantes places_restantes INT NOT NULL, CHANGE id_conducteur id_conducteur INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA986EDF194 FOREIGN KEY (id_conducteur) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_FF2B5BA986EDF194 ON trajets (id_conducteur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D6C1C61');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0236D04AD');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FD8AD0B');
        $this->addSql('DROP INDEX IDX_8F91ABF0D6C1C61 ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0236D04AD ON avis');
        $this->addSql('DROP INDEX IDX_8F91ABF0FD8AD0B ON avis');
        $this->addSql('ALTER TABLE avis CHANGE id_trajet id_trajet INT NOT NULL, CHANGE id_auteur id_auteur INT NOT NULL, CHANGE id_cible id_cible INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D6C1C61');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EF2FC27C');
        $this->addSql('DROP INDEX IDX_42C84955D6C1C61 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955EF2FC27C ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE id_trajet id_trajet INT NOT NULL, CHANGE id_passager id_passager INT NOT NULL');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA986EDF194');
        $this->addSql('DROP INDEX IDX_FF2B5BA986EDF194 ON trajets');
        $this->addSql('ALTER TABLE trajets CHANGE places_restantes places_restantes VARCHAR(255) NOT NULL, CHANGE id_conducteur id_conducteur VARCHAR(255) NOT NULL');
    }
}
