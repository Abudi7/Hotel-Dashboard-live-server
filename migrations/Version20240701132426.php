<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701132426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lostitem ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lostitem ADD CONSTRAINT FK_202395F3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_202395F3A76ED395 ON lostitem (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lostitem DROP FOREIGN KEY FK_202395F3A76ED395');
        $this->addSql('DROP INDEX IDX_202395F3A76ED395 ON lostitem');
        $this->addSql('ALTER TABLE lostitem DROP user_id');
    }
}
