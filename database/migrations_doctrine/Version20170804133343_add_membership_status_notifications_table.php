<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170804133343_add_membership_status_notifications_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE membership_status_notifications (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, account_id INT UNSIGNED DEFAULT NULL, time_issued DATETIME NOT NULL, time_cleared DATETIME DEFAULT NULL, cleared_reason VARCHAR(255) DEFAULT NULL, INDEX IDX_3ACFFDCAA76ED395 (user_id), INDEX IDX_3ACFFDCA9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE membership_status_notifications ADD CONSTRAINT FK_3ACFFDCAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE membership_status_notifications ADD CONSTRAINT FK_3ACFFDCA9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE membership_status_notifications');
    }
}
