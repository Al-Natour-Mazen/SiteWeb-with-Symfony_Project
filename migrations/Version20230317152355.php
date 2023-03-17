<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317152355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE i23_produits ADD COLUMN description VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__i23_produits AS SELECT id, libelle, prix_unitaire, quantite FROM i23_produits');
        $this->addSql('DROP TABLE i23_produits');
        $this->addSql('CREATE TABLE i23_produits (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL --en euros
        , quantite INTEGER NOT NULL)');
        $this->addSql('INSERT INTO i23_produits (id, libelle, prix_unitaire, quantite) SELECT id, libelle, prix_unitaire, quantite FROM __temp__i23_produits');
        $this->addSql('DROP TABLE __temp__i23_produits');
    }
}
