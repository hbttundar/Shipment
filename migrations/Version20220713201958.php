<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713201958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipment (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, carrier_id INT NOT NULL, distance INT NOT NULL, time INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2CB20DC979B1AD6 (company_id), INDEX IDX_2CB20DC21DFC797 (carrier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_location (shipment_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_F8C976AA7BE036FC (shipment_id), INDEX IDX_F8C976AA64D218E (location_id), PRIMARY KEY(shipment_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE shipment ADD CONSTRAINT FK_2CB20DC21DFC797 FOREIGN KEY (carrier_id) REFERENCES carrier (id)');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA7BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment_location DROP FOREIGN KEY FK_F8C976AA7BE036FC');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE shipment_location');
    }
}
