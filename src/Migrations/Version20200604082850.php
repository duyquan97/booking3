<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200604082850 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE guest_id guest_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE room_id room_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guests ADD CONSTRAINT FK_4D11BCB2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4D11BCB2A76ED395 ON guests (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking CHANGE guest_id guest_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE room_id room_id INT NOT NULL');
        $this->addSql('ALTER TABLE guests DROP FOREIGN KEY FK_4D11BCB2A76ED395');
        $this->addSql('DROP INDEX IDX_4D11BCB2A76ED395 ON guests');
    }
}
