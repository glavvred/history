<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420153108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursion_route_report ADD reporter_id INT NOT NULL, ADD region_id INT NOT NULL, CHANGE used used TINYINT(1) NOT NULL, ADD short_description TINYTEXT NOT NULL;
            ALTER TABLE excursion_route_report ADD CONSTRAINT FK_840C020CE1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id);
            ALTER TABLE excursion_route_report ADD CONSTRAINT FK_840C020C98260155 FOREIGN KEY (region_id) REFERENCES region (id);
            CREATE INDEX IDX_840C020CE1CFE6F5 ON excursion_route_report (reporter_id);
            CREATE INDEX IDX_840C020C98260155 ON excursion_route_report (region_id);
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE excursion_route_report DROP FOREIGN KEY FK_840C020CE1CFE6F5;
            ALTER TABLE excursion_route_report DROP FOREIGN KEY FK_840C020C98260155;
            DROP INDEX IDX_840C020CE1CFE6F5 ON excursion_route_report;
            DROP INDEX IDX_840C020C98260155 ON excursion_route_report;
            ALTER TABLE excursion_route_report DROP reporter_id, DROP region_id, CHANGE used used TINYINT(1) DEFAULT NULL;
        SQL);
    }
}
