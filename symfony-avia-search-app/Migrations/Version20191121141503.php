<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191121141503 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE location (id INT NOT NULL, airport_name VARCHAR(255) DEFAULT NULL, type VARCHAR(33) DEFAULT NULL, continent_iso VARCHAR(2) DEFAULT NULL, country_iso VARCHAR(2) DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, region_iso VARCHAR(10) DEFAULT NULL, region_name VARCHAR(255) DEFAULT NULL, gps_code VARCHAR(6) DEFAULT NULL, iata_code VARCHAR(3) DEFAULT NULL, local_code VARCHAR(6) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE location_id_seq CASCADE');
        $this->addSql('DROP TABLE location');
    }
}
