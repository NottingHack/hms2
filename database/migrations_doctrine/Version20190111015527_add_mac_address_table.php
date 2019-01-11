<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111015527_add_mac_address_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE addresses (id INT UNSIGNED AUTO_INCREMENT NOT NULL, mac_address VARCHAR(100) NOT NULL, last_seen DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ignore_addr TINYINT(1) DEFAULT \'0\' NOT NULL, comments VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FCA7516B728E969 (mac_address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE addresses');
    }
}
