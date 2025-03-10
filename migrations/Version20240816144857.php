<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240816144857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, short VARCHAR(15) NOT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, main_photo VARCHAR(255) NOT NULL, main_page TINYINT(1) DEFAULT NULL, bottom_page TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event_collection_public_event (event_collection_id INT NOT NULL, public_event_id INT NOT NULL, INDEX IDX_DAB9732D7510E651 (event_collection_id), INDEX IDX_DAB9732D54C3E474 (public_event_id), PRIMARY KEY(event_collection_id, public_event_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event_report (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, duration INT DEFAULT NULL, address VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, main_photo VARCHAR(255) DEFAULT NULL, additional_photos JSON DEFAULT NULL, prequisites LONGTEXT DEFAULT NULL, toll VARCHAR(512) DEFAULT NULL, used TINYINT(1) NOT NULL, reporter_id INT NOT NULL, category_id INT NOT NULL, region_id INT NOT NULL, INDEX IDX_F6B600D6E1CFE6F5 (reporter_id), INDEX IDX_F6B600D612469DE2 (category_id), INDEX IDX_F6B600D698260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE filter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, short VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news_letter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, coordinates VARCHAR(255) DEFAULT NULL, main_photo VARCHAR(255) NOT NULL, additional_photos JSON DEFAULT NULL, description LONGTEXT NOT NULL, contacts JSON DEFAULT NULL, verified TINYINT(1) DEFAULT NULL, short_description VARCHAR(255) NOT NULL, category_id INT NOT NULL, INDEX IDX_E6E132B412469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organisation_user (organisation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CFD7D6519E6B1585 (organisation_id), INDEX IDX_CFD7D651A76ED395 (user_id), PRIMARY KEY(organisation_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organisation_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, short VARCHAR(15) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE public_event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, duration INT DEFAULT NULL, address VARCHAR(255) NOT NULL, vk VARCHAR(255) DEFAULT NULL, tg VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, short_description VARCHAR(255) NOT NULL, main_photo VARCHAR(255) NOT NULL, additional_photos JSON DEFAULT NULL, prequisites LONGTEXT DEFAULT NULL, toll VARCHAR(512) DEFAULT NULL, created_at DATETIME NOT NULL, url VARCHAR(255) DEFAULT NULL, url_text VARCHAR(255) DEFAULT NULL, owner_id INT NOT NULL, category_id INT NOT NULL, region_id INT NOT NULL, organisation_id INT DEFAULT NULL, views INT DEFAULT NULL, INDEX IDX_CA01B5A77E3C61F9 (owner_id), INDEX IDX_CA01B5A712469DE2 (category_id), INDEX IDX_CA01B5A798260155 (region_id), INDEX IDX_CA01B5A79E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE public_event_filter (public_event_id INT NOT NULL, filter_id INT NOT NULL, INDEX IDX_6A6CF06A54C3E474 (public_event_id), INDEX IDX_6A6CF06AD395B25E (filter_id), PRIMARY KEY(public_event_id, filter_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lng VARCHAR(255) DEFAULT NULL, lat VARCHAR(255) DEFAULT NULL, admin_name VARCHAR(255) DEFAULT NULL, children_id INT DEFAULT NULL, INDEX IDX_F62F1763D3D2749 (children_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, newsletter TINYINT(1) DEFAULT NULL, region_id INT DEFAULT NULL, INDEX IDX_8D93D64998260155 (region_id), UNIQUE INDEX UNIQ_IDENTIFIER_NAME (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE event_collection_public_event ADD CONSTRAINT FK_DAB9732D7510E651 FOREIGN KEY (event_collection_id) REFERENCES event_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_collection_public_event ADD CONSTRAINT FK_DAB9732D54C3E474 FOREIGN KEY (public_event_id) REFERENCES public_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_report ADD CONSTRAINT FK_F6B600D6E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event_report ADD CONSTRAINT FK_F6B600D612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE event_report ADD CONSTRAINT FK_F6B600D698260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B412469DE2 FOREIGN KEY (category_id) REFERENCES organisation_category (id)');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D6519E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D651A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE public_event ADD CONSTRAINT FK_CA01B5A77E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE public_event ADD CONSTRAINT FK_CA01B5A712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE public_event ADD CONSTRAINT FK_CA01B5A798260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE public_event ADD CONSTRAINT FK_CA01B5A79E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE public_event_filter ADD CONSTRAINT FK_6A6CF06A54C3E474 FOREIGN KEY (public_event_id) REFERENCES public_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE public_event_filter ADD CONSTRAINT FK_6A6CF06AD395B25E FOREIGN KEY (filter_id) REFERENCES filter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F1763D3D2749 FOREIGN KEY (children_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64998260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE event_collection_public_event DROP FOREIGN KEY FK_DAB9732D7510E651');
        $this->addSql('ALTER TABLE event_collection_public_event DROP FOREIGN KEY FK_DAB9732D54C3E474');
        $this->addSql('ALTER TABLE event_report DROP FOREIGN KEY FK_F6B600D6E1CFE6F5');
        $this->addSql('ALTER TABLE event_report DROP FOREIGN KEY FK_F6B600D612469DE2');
        $this->addSql('ALTER TABLE event_report DROP FOREIGN KEY FK_F6B600D698260155');
        $this->addSql('ALTER TABLE organisation DROP FOREIGN KEY FK_E6E132B412469DE2');
        $this->addSql('ALTER TABLE organisation_user DROP FOREIGN KEY FK_CFD7D6519E6B1585');
        $this->addSql('ALTER TABLE organisation_user DROP FOREIGN KEY FK_CFD7D651A76ED395');
        $this->addSql('ALTER TABLE public_event DROP FOREIGN KEY FK_CA01B5A77E3C61F9');
        $this->addSql('ALTER TABLE public_event DROP FOREIGN KEY FK_CA01B5A712469DE2');
        $this->addSql('ALTER TABLE public_event DROP FOREIGN KEY FK_CA01B5A798260155');
        $this->addSql('ALTER TABLE public_event DROP FOREIGN KEY FK_CA01B5A79E6B1585');
        $this->addSql('ALTER TABLE public_event_filter DROP FOREIGN KEY FK_6A6CF06A54C3E474');
        $this->addSql('ALTER TABLE public_event_filter DROP FOREIGN KEY FK_6A6CF06AD395B25E');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F1763D3D2749');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64998260155');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE event_collection');
        $this->addSql('DROP TABLE event_collection_public_event');
        $this->addSql('DROP TABLE event_report');
        $this->addSql('DROP TABLE filter');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_letter');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE organisation_user');
        $this->addSql('DROP TABLE organisation_category');
        $this->addSql('DROP TABLE public_event');
        $this->addSql('DROP TABLE public_event_filter');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE `user`');
    }
}
