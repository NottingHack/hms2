<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190720140458_add_electric_tables extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE electric_readings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, meter_id INT UNSIGNED DEFAULT NULL, reading INT NOT NULL, date DATE NOT NULL, INDEX IDX_206AAE626E15CA9E (meter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE electric_meters (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_10D0AD5854177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE electric_readings ADD CONSTRAINT FK_206AAE626E15CA9E FOREIGN KEY (meter_id) REFERENCES electric_meters (id)');
        $this->addSql('ALTER TABLE electric_meters ADD CONSTRAINT FK_10D0AD5854177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');

        $this->addSql(
            "INSERT INTO electric_meters (id, name, room_id) VALUES (1, 'F6', 16), (2, 'G4', 8), (3, 'G5', 1), (4, 'G6', 6)"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE electric_readings DROP FOREIGN KEY FK_206AAE626E15CA9E');
        $this->addSql('DROP TABLE electric_readings');
        $this->addSql('DROP TABLE electric_meters');
    }
}
