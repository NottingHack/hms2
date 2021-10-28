<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190806035011_alter_bank_transactions extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bank_transactions ADD transaction_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE bank_transactions ADD CONSTRAINT FK_2A30FD572FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactions (id)');
        $this->addSql('CREATE INDEX IDX_2A30FD572FC0CB0F ON bank_transactions (transaction_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bank_transactions DROP FOREIGN KEY FK_2A30FD572FC0CB0F');
        $this->addSql('DROP INDEX IDX_2A30FD572FC0CB0F ON bank_transactions');
        $this->addSql('ALTER TABLE bank_transactions DROP transaction_id');
    }
}
