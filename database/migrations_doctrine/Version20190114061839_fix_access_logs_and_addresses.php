<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190114061839_fix_access_logs_and_addresses extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_656A05A27A4676 ON access_logs');
        $this->addSql('DROP INDEX UNIQ_656A05AB5852DF3 ON access_logs');
        $this->addSql('ALTER TABLE addresses CHANGE comments comments VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_656A05A27A4676 ON access_logs (rfid_serial)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_656A05AB5852DF3 ON access_logs (pin)');
        $this->addSql('ALTER TABLE addresses CHANGE comments comments VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
