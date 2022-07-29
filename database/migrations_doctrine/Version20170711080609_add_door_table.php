<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170711080609_add_door_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE doors (id INT UNSIGNED AUTO_INCREMENT NOT NULL, side_a_zone_id INT UNSIGNED DEFAULT NULL, side_b_zone_id INT UNSIGNED DEFAULT NULL, description VARCHAR(100) NOT NULL, short_name VARCHAR(16) NOT NULL, state VARCHAR(255) NOT NULL, state_change DATETIME NOT NULL, permission_code VARCHAR(16) DEFAULT NULL, INDEX IDX_5E5B762A49451B7F (side_a_zone_id), INDEX IDX_5E5B762A70C827BA (side_b_zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A49451B7F FOREIGN KEY (side_a_zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A70C827BA FOREIGN KEY (side_b_zone_id) REFERENCES zones (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A49451B7F');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A70C827BA');
        $this->addSql('DROP TABLE doors');
    }
}
