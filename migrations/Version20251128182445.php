<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128182445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation_message (id INT AUTO_INCREMENT NOT NULL, contact_request_id INT NOT NULL, direction VARCHAR(10) NOT NULL, sender VARCHAR(255) NOT NULL, recipient VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, html_content LONGTEXT DEFAULT NULL, sent_at DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, message_id VARCHAR(255) DEFAULT NULL, in_reply_to VARCHAR(255) DEFAULT NULL, `references` LONGTEXT DEFAULT NULL, INDEX IDX_2DEB3E7585C7E132 (contact_request_id), INDEX idx_sent_at (sent_at), INDEX idx_direction (direction), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversation_message ADD CONSTRAINT FK_2DEB3E7585C7E132 FOREIGN KEY (contact_request_id) REFERENCES contact_request (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation_message DROP FOREIGN KEY FK_2DEB3E7585C7E132');
        $this->addSql('DROP TABLE conversation_message');
    }
}
