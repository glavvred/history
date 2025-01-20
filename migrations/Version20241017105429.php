<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017105429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE public_event_statistic (id INT AUTO_INCREMENT NOT NULL, map INT DEFAULT NULL, organisation INT DEFAULT NULL, button INT DEFAULT NULL, public_event_id INT NOT NULL, UNIQUE INDEX UNIQ_C4B7B3A054C3E474 (public_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE public_event CHANGE toll toll VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE public_event_statistic DROP FOREIGN KEY FK_C4B7B3A054C3E474');
        $this->addSql('DROP TABLE public_event_statistic');
        $this->addSql('ALTER TABLE public_event CHANGE toll toll VARCHAR(512) DEFAULT NULL');
    }
}
