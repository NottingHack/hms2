<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20190626181021_alter_zone_id_to_signed extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE zone_occupancy_logs CHANGE zone_id zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zones CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE zone_occupants CHANGE zone_id zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE doors CHANGE side_a_zone_id side_a_zone_id INT DEFAULT NULL, CHANGE side_b_zone_id side_b_zone_id INT DEFAULT NULL');

        // shift zone id's down by one
        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql('UPDATE zones SET id = id-1');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id-1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id-1 WHERE side_b_zone_id IS NOT NULL');

        $this->addSql('SET foreign_key_checks = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql('UPDATE zones SET id = id+1 ORDER BY ID DESC');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id+1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id+1 WHERE side_b_zone_id IS NOT NULL');

        $this->addSql('SET foreign_key_checks = 1');

        $this->addSql('ALTER TABLE doors CHANGE side_a_zone_id side_a_zone_id INT UNSIGNED DEFAULT NULL, CHANGE side_b_zone_id side_b_zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone_occupancy_logs CHANGE zone_id zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone_occupants CHANGE zone_id zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zones CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
    }
}
