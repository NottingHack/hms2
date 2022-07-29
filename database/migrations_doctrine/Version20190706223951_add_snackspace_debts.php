<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190706223951_add_snackspace_debts extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE snackspace_debts (id INT UNSIGNED AUTO_INCREMENT NOT NULL, audit_time DATETIME NOT NULL, total_debt INT DEFAULT NULL, current_debt INT DEFAULT NULL, ex_debt INT DEFAULT NULL, total_credit INT DEFAULT NULL, current_credit INT DEFAULT NULL, ex_credit INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE snackspace_debts');
    }
}
