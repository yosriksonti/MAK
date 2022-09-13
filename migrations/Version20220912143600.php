<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912143600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicule ADD is_unlimited_mileage TINYINT(1) NOT NULL, ADD is_car_insurance TINYINT(1) NOT NULL, ADD is_passenger_insurance TINYINT(1) NOT NULL, ADD is_vat TINYINT(1) NOT NULL, ADD is_free_cancel TINYINT(1) NOT NULL, ADD is_free_update TINYINT(1) NOT NULL, ADD matricule VARCHAR(255) NOT NULL, ADD carte_grise VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicule DROP is_unlimited_mileage, DROP is_car_insurance, DROP is_passenger_insurance, DROP is_vat, DROP is_free_cancel, DROP is_free_update, DROP matricule, DROP carte_grise');
    }
}
