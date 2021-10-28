<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200519002715_add_bookable_areas extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bookable_areas (id INT UNSIGNED AUTO_INCREMENT NOT NULL, building_id INT UNSIGNED DEFAULT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, max_occupancy INT UNSIGNED DEFAULT 1 NOT NULL, additional_guest_occupancy INT UNSIGNED DEFAULT 0 NOT NULL, booking_color VARCHAR(255) NOT NULL, self_bookable TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_CD8830944D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookable_areas ADD CONSTRAINT FK_CD8830944D2A7E12 FOREIGN KEY (building_id) REFERENCES buildings (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bookable_areas');
    }
}
