<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111045106_add_snackspace_purchase_payment_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE purchase_payment (transaction_id_purchase INT UNSIGNED NOT NULL, transaction_id_payment INT UNSIGNED NOT NULL, amount INT DEFAULT NULL, INDEX IDX_76F24E748D452883 (transaction_id_purchase), INDEX IDX_76F24E745C231ACC (transaction_id_payment), PRIMARY KEY(transaction_id_purchase, transaction_id_payment)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_payment ADD CONSTRAINT FK_76F24E748D452883 FOREIGN KEY (transaction_id_purchase) REFERENCES transactions (id)');
        $this->addSql('ALTER TABLE purchase_payment ADD CONSTRAINT FK_76F24E745C231ACC FOREIGN KEY (transaction_id_payment) REFERENCES transactions (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE purchase_payment');
    }
}
