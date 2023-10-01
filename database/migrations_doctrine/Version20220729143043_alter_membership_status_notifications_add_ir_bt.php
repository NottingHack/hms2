<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20220729143043_alter_membership_status_notifications_add_ir_bt extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_status_notifications ADD bank_transaction_id INT UNSIGNED DEFAULT NULL AFTER account_id, ADD issued_reason VARCHAR(30) DEFAULT \'NON_PAYMENT\' NOT NULL AFTER bank_transaction_id');
        $this->addSql('ALTER TABLE membership_status_notifications ADD CONSTRAINT FK_3ACFFDCAB898B7D6 FOREIGN KEY (bank_transaction_id) REFERENCES bank_transactions (id)');
        $this->addSql('CREATE INDEX IDX_3ACFFDCAB898B7D6 ON membership_status_notifications (bank_transaction_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_status_notifications DROP FOREIGN KEY FK_3ACFFDCAB898B7D6');
        $this->addSql('DROP INDEX IDX_3ACFFDCAB898B7D6 ON membership_status_notifications');
        $this->addSql('ALTER TABLE membership_status_notifications DROP bank_transaction_id, DROP issued_reason');
    }
}
