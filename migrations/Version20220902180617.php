<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220902180617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, add1 VARCHAR(255) NOT NULL, add2 VARCHAR(255) NOT NULL, permis VARCHAR(255) NOT NULL, date_permis DATE NOT NULL, cin VARCHAR(255) NOT NULL, date_cin DATE NOT NULL, date_naissance DATE NOT NULL, UNIQUE INDEX UNIQ_C7440455A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, vehicule_id INT NOT NULL, num VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, date_res DATE NOT NULL, date_loc DATE NOT NULL, date_retour DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, avance DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5E9E89CBDC43AF6E (num), INDEX IDX_5E9E89CB19EB6921 (client_id), INDEX IDX_5E9E89CB4A4A3511 (vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE park (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, park_id INT NOT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, boite VARCHAR(255) NOT NULL, carb VARCHAR(255) NOT NULL, nb_places INT NOT NULL, nb_portes INT NOT NULL, nb_val INT NOT NULL, caut DOUBLE PRECISION NOT NULL, clim TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, description_det LONGTEXT NOT NULL, photo_def VARCHAR(255) NOT NULL, photo_reel VARCHAR(255) NOT NULL, photo_saison VARCHAR(255) NOT NULL, INDEX IDX_292FFF1D44990C25 (park_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D44990C25 FOREIGN KEY (park_id) REFERENCES park (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB19EB6921');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D44990C25');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB4A4A3511');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE park');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicule');
    }
}
