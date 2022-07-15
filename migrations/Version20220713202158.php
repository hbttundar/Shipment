<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713202158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add index to location for city,country and postcode together';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX location_postcode_city_country_IDX USING BTREE ON location (`postcode`,`city`,`country`);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location DROP INDEX location_postcode_city_country_IDX;');
    }
}
