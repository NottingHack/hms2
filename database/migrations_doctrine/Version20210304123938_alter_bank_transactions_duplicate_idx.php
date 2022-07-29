<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210304123938_alter_bank_transactions_duplicate_idx extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX duplicate_idx ON bank_transactions');
        $this->addSql('CREATE UNIQUE INDEX duplicate_idx ON bank_transactions (bank_id, transaction_date, description, amount)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX duplicate_idx ON bank_transactions');
        $this->addSql('CREATE UNIQUE INDEX duplicate_idx ON bank_transactions (transaction_date, description, amount)');
    }
}
