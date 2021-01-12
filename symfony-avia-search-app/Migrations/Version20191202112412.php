<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202112412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE commission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE commission (id INT NOT NULL, airline VARCHAR(2) NOT NULL, airline_name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, dest_type VARCHAR(25) NOT NULL, depart_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, depart_to DATE NOT NULL, ticket_from DATE NOT NULL, ticket_to DATE NOT NULL, codeshare VARCHAR(25) NOT NULL, operated_by VARCHAR(5) NOT NULL, cc_permited BOOLEAN NOT NULL, cabin_class VARCHAR(5) NOT NULL, booking_class VARCHAR(255) NOT NULL, bsp_rate DOUBLE PRECISION NOT NULL, agent_rate INT NOT NULL, service_fee DOUBLE PRECISION NOT NULL, apply_to_infant BOOLEAN NOT NULL, origin VARCHAR(255) NOT NULL, dest VARCHAR(255) NOT NULL, vice_versa BOOLEAN NOT NULL, apply_to_tour_code BOOLEAN NOT NULL, tour_code VARCHAR(255) DEFAULT NULL, apply_to_fare_with_additional_carriers BOOLEAN NOT NULL, additional_carrier VARCHAR(5) DEFAULT NULL, carrier_type VARCHAR(255) NOT NULL, fare_basis VARCHAR(255) NOT NULL, plating_carrier VARCHAR(5) NOT NULL, permitted_flight VARCHAR(255) NOT NULL, not_permitted_flight VARCHAR(255) NOT NULL, required_flight VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE commission_id_seq CASCADE');
        $this->addSql('DROP TABLE commission');
    }
}
