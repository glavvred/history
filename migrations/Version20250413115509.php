<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413115509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE excursion_route (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, main_photo VARCHAR(255) NOT NULL, additional_photos JSON DEFAULT NULL, route VARCHAR(2048) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE o_regions
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1DD39950989D9B62 ON news (slug)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE FULLTEXT INDEX news_fulltext_idx ON news (name, description)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation RENAME INDEX name TO organisation_fulltext_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event DROP region, DROP new_region_id, CHANGE toll toll VARCHAR(255) DEFAULT NULL, CHANGE slug slug VARCHAR(128) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_CA01B5A7989D9B62 ON public_event (slug)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event RENAME INDEX idx_f62f176727aca71 TO event_fulltext_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event_statistic DROP INDEX FK_C4B7B3A054C3E477, ADD UNIQUE INDEX UNIQ_C4B7B3A0713CC9A9 (managing_organisation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE region CHANGE slug slug VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE o_regions (id INT DEFAULT NULL, coordinates VARCHAR(37) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, region VARCHAR(40) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE excursion_route
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1DD39950989D9B62 ON news
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX news_fulltext_idx ON news
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organisation RENAME INDEX organisation_fulltext_idx TO name
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_CA01B5A7989D9B62 ON public_event
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event ADD region VARCHAR(255) NOT NULL, ADD new_region_id INT NOT NULL, CHANGE toll toll VARCHAR(512) DEFAULT NULL, CHANGE slug slug VARCHAR(128) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event RENAME INDEX event_fulltext_idx TO IDX_F62F176727ACA71
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE public_event_statistic DROP INDEX UNIQ_C4B7B3A0713CC9A9, ADD INDEX FK_C4B7B3A054C3E477 (managing_organisation_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE region CHANGE slug slug VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
