<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230314160504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE i23_orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_produit INTEGER NOT NULL, id_client INTEGER NOT NULL, quantite INTEGER NOT NULL, CONSTRAINT FK_448D3E15F7384557 FOREIGN KEY (id_produit) REFERENCES i23_produits (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_448D3E15E173B1B8 FOREIGN KEY (id_client) REFERENCES i23_users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_448D3E15F7384557 ON i23_orders (id_produit)');
        $this->addSql('CREATE INDEX IDX_448D3E15E173B1B8 ON i23_orders (id_client)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_448D3E15F7384557E173B1B8 ON i23_orders (id_produit, id_client)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE i23_orders');
    }
}
