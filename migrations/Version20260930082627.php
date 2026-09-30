<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260930082627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire VARCHAR(255) NOT NULL, date DATE NOT NULL, id_trajet INT NOT NULL, id_auteur INT NOT NULL, id_cible INT NOT NULL, INDEX IDX_8F91ABF0D6C1C61 (id_trajet), INDEX IDX_8F91ABF0236D04AD (id_auteur), INDEX IDX_8F91ABF0FD8AD0B (id_cible), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) NOT NULL, date_reservation DATE NOT NULL, id_trajet INT DEFAULT NULL, id_passager INT DEFAULT NULL, INDEX IDX_42C84955D6C1C61 (id_trajet), INDEX IDX_42C84955EF2FC27C (id_passager), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trajets (id INT AUTO_INCREMENT NOT NULL, origine VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, date_heure DATETIME NOT NULL, places_totales INT NOT NULL, places_restantes INT NOT NULL, statut VARCHAR(255) NOT NULL, id_conducteur INT DEFAULT NULL, INDEX IDX_FF2B5BA986EDF194 (id_conducteur), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0236D04AD FOREIGN KEY (id_auteur) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FD8AD0B FOREIGN KEY (id_cible) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D6C1C61 FOREIGN KEY (id_trajet) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955EF2FC27C FOREIGN KEY (id_passager) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA986EDF194 FOREIGN KEY (id_conducteur) REFERENCES utilisateurs (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0D6C1C61');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0236D04AD');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FD8AD0B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D6C1C61');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955EF2FC27C');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA986EDF194');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE trajets');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
