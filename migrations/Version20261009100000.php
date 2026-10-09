<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261009100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table vehicule (propriétaire = utilisateur)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, modele VARCHAR(50) NOT NULL, couleur VARCHAR(30) DEFAULT NULL, annee SMALLINT DEFAULT NULL, immatriculation VARCHAR(15) NOT NULL, nb_places SMALLINT NOT NULL, id_proprietaire INT NOT NULL, INDEX IDX_292FFF1D4A22ECA4 (id_proprietaire), UNIQUE INDEX UNIQ_VEHICULE_IMMATRICULATION (immatriculation), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D4A22ECA4 FOREIGN KEY (id_proprietaire) REFERENCES utilisateurs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D4A22ECA4');
        $this->addSql('DROP TABLE vehicule');
    }
}
