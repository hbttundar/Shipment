<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220713204049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE shipment_location DROP FOREIGN KEY FK_F8C976AA7BE036FC');
        $this->addSql('ALTER TABLE shipment_location DROP FOREIGN KEY FK_F8C976AA64D218E');
        $this->addSql('ALTER TABLE shipment_location DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA7BE036FC FOREIGN KEY (shipment_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA64D218E FOREIGN KEY (location_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE shipment_location ADD PRIMARY KEY (location_id, shipment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE shipment_location DROP FOREIGN KEY FK_F8C976AA64D218E');
        $this->addSql('ALTER TABLE shipment_location DROP FOREIGN KEY FK_F8C976AA7BE036FC');
        $this->addSql('ALTER TABLE shipment_location DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_location ADD CONSTRAINT FK_F8C976AA7BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shipment_location ADD PRIMARY KEY (shipment_id, location_id)');
    }
}
