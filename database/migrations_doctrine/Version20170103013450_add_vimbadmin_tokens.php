<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170103013450_add_vimbadmin_tokens extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vimbadmin_tokens (id INT UNSIGNED AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, token LONGTEXT NOT NULL, expires DATETIME NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8DCFB55B4E645A7E (`key`), INDEX key_index (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE vimbadmin_tokens');
    }
}
