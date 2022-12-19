<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221218205652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, maps VARCHAR(255) NOT NULL, maps_frame VARCHAR(510) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depence (id INT AUTO_INCREMENT NOT NULL, vehicule_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, designation VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_7EC785064A4A3511 (vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, vehicule_id INT DEFAULT NULL, body LONGTEXT NOT NULL, created_on DATETIME NOT NULL, rating INT NOT NULL, visible TINYINT(1) NOT NULL, INDEX IDX_D229445819EB6921 (client_id), INDEX IDX_D22944584A4A3511 (vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, vehicule_id INT NOT NULL, agence_depart_id INT NOT NULL, agence_arrive_id INT NOT NULL, num VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, date_res DATETIME NOT NULL, date_loc DATETIME NOT NULL, date_retour DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, avance DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, is_baby_seat TINYINT(1) NOT NULL, is_personal_driver TINYINT(1) NOT NULL, is_second_driver TINYINT(1) NOT NULL, is_stw TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_5E9E89CBDC43AF6E (num), INDEX IDX_5E9E89CB19EB6921 (client_id), INDEX IDX_5E9E89CB4A4A3511 (vehicule_id), INDEX IDX_5E9E89CB209D99B2 (agence_depart_id), INDEX IDX_5E9E89CB7A9DE1B1 (agence_arrive_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, body VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, seen TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE park (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, debut_hs DATE NOT NULL, fin_hs DATE NOT NULL, debut_bs DATE NOT NULL, fin_bs DATE NOT NULL, prix_baby_seat DOUBLE PRECISION NOT NULL, prix_personal_driver DOUBLE PRECISION NOT NULL, prix_second_driver DOUBLE PRECISION NOT NULL, prix_stw DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, location_id INT NOT NULL, session_id VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, total DOUBLE PRECISION NOT NULL, created_on DATETIME NOT NULL, INDEX IDX_6D28840D19EB6921 (client_id), INDEX IDX_6D28840D64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo (id INT AUTO_INCREMENT NOT NULL, pourcentage DOUBLE PRECISION NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, pays VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, add1 VARCHAR(255) DEFAULT NULL, add2 VARCHAR(255) DEFAULT NULL, permis VARCHAR(255) DEFAULT NULL, date_permis DATE DEFAULT NULL, cin VARCHAR(255) DEFAULT NULL, date_cin DATE DEFAULT NULL, date_naissance DATE DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, park_id INT NOT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, boite VARCHAR(255) NOT NULL, carb VARCHAR(255) NOT NULL, nb_places INT NOT NULL, nb_portes INT NOT NULL, nb_val INT NOT NULL, caut DOUBLE PRECISION NOT NULL, clim TINYINT(1) NOT NULL, description VARCHAR(255) NOT NULL, description_det LONGTEXT NOT NULL, photo_def VARCHAR(255) NOT NULL, photo_reel VARCHAR(255) NOT NULL, photo_saison VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, is_unlimited_mileage TINYINT(1) NOT NULL, is_car_insurance TINYINT(1) NOT NULL, is_passenger_insurance TINYINT(1) NOT NULL, is_vat TINYINT(1) NOT NULL, matricule VARCHAR(255) NOT NULL, carte_grise VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_292FFF1D44990C25 (park_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE depence ADD CONSTRAINT FK_7EC785064A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D229445819EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944584A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB19EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB4A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB209D99B2 FOREIGN KEY (agence_depart_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB7A9DE1B1 FOREIGN KEY (agence_arrive_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D19EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D44990C25 FOREIGN KEY (park_id) REFERENCES park (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depence DROP FOREIGN KEY FK_7EC785064A4A3511');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D229445819EB6921');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944584A4A3511');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB19EB6921');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB4A4A3511');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB209D99B2');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB7A9DE1B1');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D19EB6921');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D64D218E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D44990C25');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE depence');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE park');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE promo');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
