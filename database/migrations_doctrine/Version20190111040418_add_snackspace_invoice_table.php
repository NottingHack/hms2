<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111040418_add_snackspace_invoice_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoices (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, `from` DATETIME NOT NULL, `to` DATETIME NOT NULL, generated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, status VARCHAR(16) NOT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_6A2F2F95A832C1C9 (email_id), INDEX IDX_6A2F2F95A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F95A832C1C9 FOREIGN KEY (email_id) REFERENCES snackspace_emails (id)');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invoices');
    }
}
