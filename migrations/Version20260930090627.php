<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260930090627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire VARCHAR(255) NOT NULL, date DATE NOT NULL, id_trajet INT NOT NULL, id_auteur INT NOT NULL, id_cible INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trajets (id INT AUTO_INCREMENT NOT NULL, origine VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, date_heure DATETIME NOT NULL, places_totales INT NOT NULL, places_restantes INT NOT NULL, statut VARCHAR(255) NOT NULL, id_conducteur INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD date_reservation DATE NOT NULL, DROP date_heure, CHANGE id_trajet id_trajet INT NOT NULL, CHANGE id_passager id_passager INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE trajets');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE reservation ADD date_heure DATETIME NOT NULL, DROP date_reservation, CHANGE id_trajet id_trajet VARCHAR(255) NOT NULL, CHANGE id_passager id_passager VARCHAR(255) NOT NULL');
    }
}
