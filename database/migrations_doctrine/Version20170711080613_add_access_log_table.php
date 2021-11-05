<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170711080613_add_access_log_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE access_logs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, door_id INT UNSIGNED DEFAULT NULL, access_time DATETIME NOT NULL, rfid_serial VARCHAR(50) DEFAULT NULL, pin VARCHAR(12) DEFAULT NULL, access_result INT DEFAULT 0 NOT NULL, denied_reason VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_656A05A27A4676 (rfid_serial), UNIQUE INDEX UNIQ_656A05AB5852DF3 (pin), INDEX IDX_656A05AA76ED395 (user_id), INDEX IDX_656A05A58639EAE (door_id), INDEX access_time_index (access_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_logs ADD CONSTRAINT FK_656A05AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE access_logs ADD CONSTRAINT FK_656A05A58639EAE FOREIGN KEY (door_id) REFERENCES doors (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_logs DROP FOREIGN KEY FK_656A05A58639EAE');
        $this->addSql('DROP TABLE access_logs');
    }
}
