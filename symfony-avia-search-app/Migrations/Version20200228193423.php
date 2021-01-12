<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200228193423 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE price_rule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE price_rule (id INT NOT NULL, fixed INT NOT NULL, percent INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE commission ADD price_rule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commission ADD CONSTRAINT FK_1C6501581F68CB0D FOREIGN KEY (price_rule_id) REFERENCES price_rule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1C6501581F68CB0D ON commission (price_rule_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commission DROP CONSTRAINT FK_1C6501581F68CB0D');
        $this->addSql('DROP SEQUENCE price_rule_id_seq CASCADE');
        $this->addSql('DROP TABLE price_rule');
        $this->addSql('DROP INDEX IDX_1C6501581F68CB0D');
        $this->addSql('ALTER TABLE commission DROP price_rule_id');
    }
}
