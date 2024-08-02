<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802211601_add_instrumentation_sensor_tables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Instrumentation sensors tables for Humidity, Pressure and Battery';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE barometric_pressures (sensor VARCHAR(30) NOT NULL, name VARCHAR(100) DEFAULT NULL, reading DOUBLE PRECISION DEFAULT NULL, `time` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(sensor)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humidities (sensor VARCHAR(30) NOT NULL, name VARCHAR(100) DEFAULT NULL, reading DOUBLE PRECISION DEFAULT NULL, `time` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(sensor)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor_batteries (sensor VARCHAR(30) NOT NULL, name VARCHAR(100) DEFAULT NULL, reading DOUBLE PRECISION DEFAULT NULL, `time` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(sensor)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE barometric_pressures');
        $this->addSql('DROP TABLE humidities');
        $this->addSql('DROP TABLE sensor_batteries');
    }
}
