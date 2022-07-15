<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713202349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add index to carrier table for name and email together';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX carrier_name_email_IDX USING BTREE ON carrier (`name`,`email`);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE carrier DROP INDEX carrier_name_email_IDX;');
    }
}
