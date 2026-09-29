<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260929231141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(255) NOT NULL, date_reservation DATE NOT NULL, id_trajet INT NOT NULL, id_passager INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis CHANGE id_trajet id_trajet INT NOT NULL, CHANGE id_auteur id_auteur INT NOT NULL, CHANGE id_cible id_cible INT NOT NULL');
        $this->addSql('ALTER TABLE trajets CHANGE places_restantes places_restantes INT NOT NULL, CHANGE id_conducteur id_conducteur INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('ALTER TABLE avis CHANGE id_trajet id_trajet VARCHAR(255) NOT NULL, CHANGE id_auteur id_auteur VARCHAR(255) NOT NULL, CHANGE id_cible id_cible VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE trajets CHANGE places_restantes places_restantes VARCHAR(255) NOT NULL, CHANGE id_conducteur id_conducteur VARCHAR(255) NOT NULL');
    }
}
