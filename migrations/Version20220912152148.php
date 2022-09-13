<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912152148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location ADD is_baby_seat TINYINT(1) NOT NULL, ADD is_personal_driver TINYINT(1) NOT NULL, ADD is_second_driver TINYINT(1) NOT NULL, ADD is_stw TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE vehicule ADD prix DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP is_baby_seat, DROP is_personal_driver, DROP is_second_driver, DROP is_stw');
        $this->addSql('ALTER TABLE vehicule DROP prix');
    }
}
