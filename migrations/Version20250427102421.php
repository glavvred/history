<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427102421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursion_route ADD coordinates VARCHAR(255) DEFAULT NULL;
            ALTER TABLE excursion_route_report ADD coordinates VARCHAR(255) DEFAULT NULL;
            ALTER TABLE public_event ADD time time DEFAULT NULL;
            ALTER TABLE public_event ADD follow_time boolean DEFAULT NULL;
            ALTER TABLE organisation ADD time_open time DEFAULT NULL;
            ALTER TABLE organisation ADD time_close time DEFAULT NULL;
            ALTER TABLE organisation ADD alternate_names json DEFAULT NULL;

        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursion_route DROP coordinates;
            ALTER TABLE excursion_route_report DROP coordinates;
            ALTER TABLE public_event DROP time;
            ALTER TABLE public_event DROP follow_time;
            ALTER TABLE organisation DROP time_open;
            ALTER TABLE organisation DROP time_close;
            ALTER TABLE organisation DROP alternate_names;
        SQL);
    }
}
