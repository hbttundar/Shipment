<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713202822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX carrier_name_email_IDX ON carrier');
        $this->addSql('DROP INDEX company_name_email_IDX ON company');
        $this->addSql('DROP INDEX location_postcode_city_country_IDX ON location');
        $this->addSql('ALTER TABLE shipment ADD price DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX carrier_name_email_IDX ON carrier (name, email)');
        $this->addSql('CREATE INDEX company_name_email_IDX ON company (name, email)');
        $this->addSql('CREATE INDEX location_postcode_city_country_IDX ON location (postcode, city, country)');
        $this->addSql('ALTER TABLE shipment DROP price');
    }
}
