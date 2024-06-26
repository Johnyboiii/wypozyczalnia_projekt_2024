<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607203151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ADD reserved_by_id INT DEFAULT NULL, ADD reservation_status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597BCDB4AF4 FOREIGN KEY (reserved_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_50586597BCDB4AF4 ON tasks (reserved_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597BCDB4AF4');
        $this->addSql('DROP INDEX IDX_50586597BCDB4AF4 ON tasks');
        $this->addSql('ALTER TABLE tasks DROP reserved_by_id, DROP reservation_status');
    }
}
