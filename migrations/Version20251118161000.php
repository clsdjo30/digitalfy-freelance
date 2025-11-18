<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial migration for Digitalfy application
 */
final class Version20251118161000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database schema for Blog, Category, Project, and ContactRequest';
    }

    public function up(Schema $schema): void
    {
        // Category table
        $this->addSql('CREATE TABLE category (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Blog Post table
        $this->addSql('CREATE TABLE blog_post (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            excerpt LONGTEXT DEFAULT NULL,
            content LONGTEXT NOT NULL,
            featured_image VARCHAR(255) DEFAULT NULL,
            published_at DATETIME DEFAULT NULL,
            status VARCHAR(20) NOT NULL,
            meta_title VARCHAR(255) DEFAULT NULL,
            meta_description LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_BA5AE01D989D9B62 (slug),
            INDEX IDX_BA5AE01D12469DE2 (category_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D12469DE2
            FOREIGN KEY (category_id) REFERENCES category (id)');

        // Project table
        $this->addSql('CREATE TABLE project (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            thumbnail VARCHAR(255) DEFAULT NULL,
            technologies JSON DEFAULT NULL,
            context LONGTEXT DEFAULT NULL,
            solution LONGTEXT DEFAULT NULL,
            results LONGTEXT DEFAULT NULL,
            published TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_2FB3D0EE989D9B62 (slug),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Contact Request table
        $this->addSql('CREATE TABLE contact_request (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            project_type VARCHAR(100) NOT NULL,
            estimated_budget VARCHAR(100) DEFAULT NULL,
            message LONGTEXT NOT NULL,
            submitted_at DATETIME NOT NULL,
            status VARCHAR(20) NOT NULL,
            notes LONGTEXT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01D12469DE2');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE contact_request');
    }
}
