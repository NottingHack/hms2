<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190108233223_alter_bank_transactions_to_pennies extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE bank_transactions SET amount = amount*100');
        $this->addSql('ALTER TABLE bank_transactions CHANGE amount amount INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bank_transactions CHANGE amount amount NUMERIC(8, 2) DEFAULT NULL');
        $this->addSql('UPDATE bank_transactions SET amount = amount/100');
    }
}
