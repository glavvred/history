<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126085226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE o_regions');
        $this->addSql('DROP TABLE pe_regions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A4A3BC1D989D9B62 ON event_collection (slug)');
        $this->addSql('ALTER TABLE organisation CHANGE slug slug VARCHAR(128) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6E132B4989D9B62 ON organisation (slug)');
        $this->addSql('ALTER TABLE public_event DROP region');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CA01B5A7989D9B62 ON public_event (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE o_regions (id INT DEFAULT NULL, coordinates VARCHAR(37) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, region VARCHAR(40) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE pe_regions (id INT DEFAULT NULL, region VARCHAR(38) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP INDEX UNIQ_A4A3BC1D989D9B62 ON event_collection');
        $this->addSql('DROP INDEX UNIQ_E6E132B4989D9B62 ON organisation');
        $this->addSql('ALTER TABLE organisation CHANGE slug slug VARCHAR(128) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_CA01B5A7989D9B62 ON public_event');
        $this->addSql('ALTER TABLE public_event ADD region VARCHAR(255) NOT NULL');
    }
}
