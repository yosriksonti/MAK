<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220920175043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, maps VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD agence_depart_id INT NOT NULL, ADD agence_arrive_id INT NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB209D99B2 FOREIGN KEY (agence_depart_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB7A9DE1B1 FOREIGN KEY (agence_arrive_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB209D99B2 ON location (agence_depart_id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB7A9DE1B1 ON location (agence_arrive_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB209D99B2');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB7A9DE1B1');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP INDEX IDX_5E9E89CB209D99B2 ON location');
        $this->addSql('DROP INDEX IDX_5E9E89CB7A9DE1B1 ON location');
        $this->addSql('ALTER TABLE location DROP agence_depart_id, DROP agence_arrive_id');
    }
}
