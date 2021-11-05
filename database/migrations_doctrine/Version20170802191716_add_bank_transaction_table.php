<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170802191716_add_bank_transaction_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bank_transactions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bank_id INT UNSIGNED DEFAULT NULL, account_id INT UNSIGNED DEFAULT NULL, transaction_date DATE NOT NULL, description VARCHAR(255) NOT NULL, amount NUMERIC(8, 2) DEFAULT NULL, INDEX IDX_2A30FD5711C8FB41 (bank_id), INDEX IDX_2A30FD579B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_transactions ADD CONSTRAINT FK_2A30FD5711C8FB41 FOREIGN KEY (bank_id) REFERENCES banks (id)');
        $this->addSql('ALTER TABLE bank_transactions ADD CONSTRAINT FK_2A30FD579B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bank_transactions');
    }
}
