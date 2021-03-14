<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170531080558_add_rfid_tags_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rfid_tags (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, rfid_serial VARCHAR(50) DEFAULT NULL, rfid_serial_legacy VARCHAR(50) DEFAULT NULL, state INT DEFAULT 0 NOT NULL, last_used DATETIME DEFAULT NULL, friendly_name VARCHAR(128) DEFAULT NULL, UNIQUE INDEX UNIQ_5728019B27A4676 (rfid_serial), UNIQUE INDEX UNIQ_5728019BA12AD2E8 (rfid_serial_legacy), INDEX IDX_5728019BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rfid_tags ADD CONSTRAINT FK_5728019BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rfid_tags');
    }
}
