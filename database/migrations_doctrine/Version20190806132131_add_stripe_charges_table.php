<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190806132131_add_stripe_charges_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stripe_charges (id VARCHAR(140) NOT NULL, transaction_id INT UNSIGNED DEFAULT NULL, refund_transaction_id INT UNSIGNED DEFAULT NULL, withdrawn_transaction_id INT UNSIGNED DEFAULT NULL, reinstated_transaction_id INT UNSIGNED DEFAULT NULL, payment_intent_id VARCHAR(255) DEFAULT NULL, refund_id VARCHAR(255) DEFAULT NULL, dispute_id VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, amount INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_152861E02FC0CB0F (transaction_id), INDEX IDX_152861E0A99A014E (refund_transaction_id), INDEX IDX_152861E04484ADA4 (withdrawn_transaction_id), INDEX IDX_152861E0ED11AA78 (reinstated_transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stripe_charges ADD CONSTRAINT FK_152861E02FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactions (id)');
        $this->addSql('ALTER TABLE stripe_charges ADD CONSTRAINT FK_152861E0A99A014E FOREIGN KEY (refund_transaction_id) REFERENCES transactions (id)');
        $this->addSql('ALTER TABLE stripe_charges ADD CONSTRAINT FK_152861E04484ADA4 FOREIGN KEY (withdrawn_transaction_id) REFERENCES transactions (id)');
        $this->addSql('ALTER TABLE stripe_charges ADD CONSTRAINT FK_152861E0ED11AA78 FOREIGN KEY (reinstated_transaction_id) REFERENCES transactions (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE stripe_charges');
    }
}
