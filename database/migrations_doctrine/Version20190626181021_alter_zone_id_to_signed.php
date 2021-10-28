<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190626181021_alter_zone_id_to_signed extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A49451B7F');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A70C827BA');
        $this->addSql('ALTER TABLE zone_occupancy_logs DROP FOREIGN KEY FK_59ADE3689F2C3FAB');
        $this->addSql('ALTER TABLE zone_occupants DROP FOREIGN KEY FK_EECE6E0B9F2C3FAB');

        $this->addSql('ALTER TABLE zone_occupancy_logs CHANGE zone_id zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zones CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE zone_occupants CHANGE zone_id zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE doors CHANGE side_a_zone_id side_a_zone_id INT DEFAULT NULL, CHANGE side_b_zone_id side_b_zone_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE zone_occupants ADD CONSTRAINT FK_EECE6E0B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE zone_occupancy_logs ADD CONSTRAINT FK_59ADE3689F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A70C827BA FOREIGN KEY (side_a_zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A49451B7F FOREIGN KEY (side_b_zone_id) REFERENCES zones (id)');

        // shift zone id's down by one
        $this->addSql('UPDATE zones SET id = id-1 ORDER BY id ASC');
        $this->addSql('UPDATE zone_occupancy_logs SET zone_id = zone_id-1');
        $this->addSql('UPDATE zone_occupants SET zone_id = zone_id-1');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id-1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id-1 WHERE side_b_zone_id IS NOT NULL');

        $this->addSql('SET foreign_key_checks = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        // shift zone id's down by one
        $this->addSql('UPDATE zones SET id = id+1 ORDER BY id DESC');
        $this->addSql('UPDATE zone_occupancy_logs SET zone_id = zone_id+1');
        $this->addSql('UPDATE zone_occupants SET zone_id = zone_id+1');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id+1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id+1 WHERE side_b_zone_id IS NOT NULL');

        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A49451B7F');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A70C827BA');
        $this->addSql('ALTER TABLE zone_occupancy_logs DROP FOREIGN KEY FK_59ADE3689F2C3FAB');
        $this->addSql('ALTER TABLE zone_occupants DROP FOREIGN KEY FK_EECE6E0B9F2C3FAB');

        $this->addSql('ALTER TABLE doors CHANGE side_a_zone_id side_a_zone_id INT UNSIGNED DEFAULT NULL, CHANGE side_b_zone_id side_b_zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone_occupancy_logs CHANGE zone_id zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone_occupants CHANGE zone_id zone_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zones CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');

        $this->addSql('ALTER TABLE zone_occupants ADD CONSTRAINT FK_EECE6E0B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE zone_occupancy_logs ADD CONSTRAINT FK_59ADE3689F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A70C827BA FOREIGN KEY (side_a_zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A49451B7F FOREIGN KEY (side_b_zone_id) REFERENCES zones (id)');

        $this->addSql('SET foreign_key_checks = 1');
    }
}
