<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522141722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD rooms_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8E2368AB FOREIGN KEY (rooms_id) REFERENCES rooms (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE8E2368AB ON booking (rooms_id)');
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A963301C60');
        $this->addSql('DROP INDEX IDX_7CA11A963301C60 ON rooms');
        $this->addSql('ALTER TABLE rooms DROP booking_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE8E2368AB');
        $this->addSql('DROP INDEX IDX_E00CEDDE8E2368AB ON booking');
        $this->addSql('ALTER TABLE booking DROP rooms_id');
        $this->addSql('ALTER TABLE rooms ADD booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A963301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7CA11A963301C60 ON rooms (booking_id)');
    }
}
