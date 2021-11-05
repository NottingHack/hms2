<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20161107022054_add_invite extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invites (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, invite_token VARCHAR(100) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C7E210D7E7927C74 (email), INDEX email_index (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invites');
    }
}
