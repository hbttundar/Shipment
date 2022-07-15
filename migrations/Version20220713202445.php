<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713202445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add index to company table for name and email together';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX company_name_email_IDX USING BTREE ON company (`name`,`email`);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company DROP INDEX company_name_email_IDX;');
    }
}
