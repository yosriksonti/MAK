<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906002536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('ALTER TABLE location ADD client_id INT NOT NULL, ADD vehicule_id INT NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB19EB6921 ON location (client_id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB4A4A3511 ON location (vehicule_id)');
        $this->addSql('ALTER TABLE park ADD nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE vehicule ADD park_id INT NOT NULL');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D44990C25 FOREIGN KEY (park_id) REFERENCES park (id)');
        $this->addSql('CREATE INDEX IDX_292FFF1D44990C25 ON vehicule (park_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395 ON client');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB19EB6921');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB4A4A3511');
        $this->addSql('DROP INDEX IDX_5E9E89CB19EB6921 ON location');
        $this->addSql('DROP INDEX IDX_5E9E89CB4A4A3511 ON location');
        $this->addSql('ALTER TABLE location DROP client_id, DROP vehicule_id');
        $this->addSql('ALTER TABLE park DROP nom');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D44990C25');
        $this->addSql('DROP INDEX IDX_292FFF1D44990C25 ON vehicule');
        $this->addSql('ALTER TABLE vehicule DROP park_id');
    }
}
