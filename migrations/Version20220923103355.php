<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220923103355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feedback ADD vehicule_id INT DEFAULT NULL, ADD visible TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944584A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('CREATE INDEX IDX_D22944584A4A3511 ON feedback (vehicule_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944584A4A3511');
        $this->addSql('DROP INDEX IDX_D22944584A4A3511 ON feedback');
        $this->addSql('ALTER TABLE feedback DROP vehicule_id, DROP visible');
    }
}
