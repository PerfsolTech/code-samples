<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200301093819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE commission ALTER operated_by DROP NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER booking_class DROP NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER permitted_flight DROP NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER not_permitted_flight DROP NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER required_flight DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commission ALTER operated_by SET NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER booking_class SET NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER permitted_flight SET NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER not_permitted_flight SET NOT NULL');
        $this->addSql('ALTER TABLE commission ALTER required_flight SET NOT NULL');
    }
}
