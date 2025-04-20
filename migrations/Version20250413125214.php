<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413125214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursion_route ADD slug VARCHAR(255) NOT NULL, ADD published TINYINT(1) DEFAULT NULL, ADD short_description LONGTEXT NOT NULL
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
            ALTER TABLE excursion_route DROP slug, DROP published, DROP short_description
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
