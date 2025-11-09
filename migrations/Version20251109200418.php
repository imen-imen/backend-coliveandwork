<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109200418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8148CE3250');
        $this->addSql('DROP INDEX IDX_D4E6F8148CE3250 ON address');
        $this->addSql('ALTER TABLE address DROP coliving_city_id');
        $this->addSql('ALTER TABLE amenity DROP icon_url, CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('DROP INDEX `primary` ON coliving_space_amenity');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD PRIMARY KEY (coliving_space_id, amenity_id)');
        $this->addSql('ALTER TABLE photo CHANGE is_main is_main TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE private_space CHANGE is_active is_active TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE total_price total_price NUMERIC(7, 2) NOT NULL');
        $this->addSql('ALTER TABLE review CHANGE rating rating NUMERIC(2, 1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address ADD coliving_city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8148CE3250 FOREIGN KEY (coliving_city_id) REFERENCES address (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D4E6F8148CE3250 ON address (coliving_city_id)');
        $this->addSql('ALTER TABLE amenity ADD icon_url VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON coliving_space_amenity');
        $this->addSql('ALTER TABLE coliving_space_amenity ADD PRIMARY KEY (amenity_id, coliving_space_id)');
        $this->addSql('ALTER TABLE photo CHANGE is_main is_main TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE private_space CHANGE is_active is_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE total_price total_price NUMERIC(6, 2) NOT NULL');
        $this->addSql('ALTER TABLE review CHANGE rating rating NUMERIC(3, 2) NOT NULL');
    }
}
