<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20181227172848_add_tools_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tools (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, restrictions VARCHAR(20) NOT NULL, status_text VARCHAR(255) DEFAULT NULL, pph INT NOT NULL, booking_length INT NOT NULL, length_max INT NOT NULL, bookings_max INT NOT NULL, UNIQUE INDEX UNIQ_EAFADE775E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tools');
    }
}
