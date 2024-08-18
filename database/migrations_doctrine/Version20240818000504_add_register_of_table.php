<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240818000504_add_register_of_table extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds Register Of Members and Register Of Directors tables.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE register_of_directors (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address_1 VARCHAR(100) DEFAULT NULL, address_2 VARCHAR(100) DEFAULT NULL, address_3 VARCHAR(100) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_county VARCHAR(100) DEFAULT NULL, address_postcode VARCHAR(10) DEFAULT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_35DE90FBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE register_of_members (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_ED58F774A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE register_of_directors ADD CONSTRAINT FK_35DE90FBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE register_of_members ADD CONSTRAINT FK_ED58F774A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE register_of_directors DROP FOREIGN KEY FK_35DE90FBA76ED395');
        $this->addSql('ALTER TABLE register_of_members DROP FOREIGN KEY FK_ED58F774A76ED395');
        $this->addSql('DROP TABLE register_of_directors');
        $this->addSql('DROP TABLE register_of_members');
    }
}
