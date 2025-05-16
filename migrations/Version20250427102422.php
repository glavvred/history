<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427102422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER table public_event DROP INDEX IDX_F62F176727ACA71;
            ALTER table public_event ADD INDEX IDX_F62F176727ACA71 (`name`, `description`, `address`);         
            
            ALTER table organisation DROP INDEX name;
            ALTER TABLE organisation ADD FULLTEXT `name` (`name`, `description`, `address`);


        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            
        SQL);
    }
}
