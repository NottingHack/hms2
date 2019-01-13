<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190112002932_add_vending_tables extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vending_locations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vending_machine_id INT UNSIGNED DEFAULT NULL, product_id INT UNSIGNED DEFAULT NULL, encoding VARCHAR(10) NOT NULL, name VARCHAR(10) NOT NULL, INDEX IDX_FB51EA3982EA3E1C (vending_machine_id), INDEX IDX_FB51EA394584665A (product_id), UNIQUE INDEX vending_locations_unique_idx (id, encoding), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vending_machines (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description VARCHAR(100) DEFAULT NULL, type VARCHAR(10) DEFAULT NULL, connection VARCHAR(10) DEFAULT NULL, address VARCHAR(100) DEFAULT NULL, UNIQUE INDEX vending_machines_uniqie_idx (connection, address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vend_logs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vending_machine_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, transaction_id INT UNSIGNED DEFAULT NULL, rfid_serial VARCHAR(50) DEFAULT NULL, enqueued_time DATETIME DEFAULT NULL, request_time DATETIME DEFAULT NULL, success_time DATETIME DEFAULT NULL, cancelled_time DATETIME DEFAULT NULL, failed_time DATETIME DEFAULT NULL, amount_scaled INT DEFAULT NULL, position VARCHAR(10) DEFAULT NULL, denied_reason VARCHAR(100) DEFAULT NULL, INDEX IDX_DEDF3B8082EA3E1C (vending_machine_id), INDEX IDX_DEDF3B80A76ED395 (user_id), INDEX IDX_DEDF3B802FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vending_locations ADD CONSTRAINT FK_FB51EA3982EA3E1C FOREIGN KEY (vending_machine_id) REFERENCES vending_machines (id)');
        $this->addSql('ALTER TABLE vending_locations ADD CONSTRAINT FK_FB51EA394584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE vend_logs ADD CONSTRAINT FK_DEDF3B8082EA3E1C FOREIGN KEY (vending_machine_id) REFERENCES vending_machines (id)');
        $this->addSql('ALTER TABLE vend_logs ADD CONSTRAINT FK_DEDF3B80A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vend_logs ADD CONSTRAINT FK_DEDF3B802FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transactions (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vending_locations DROP FOREIGN KEY FK_FB51EA3982EA3E1C');
        $this->addSql('ALTER TABLE vend_logs DROP FOREIGN KEY FK_DEDF3B8082EA3E1C');
        $this->addSql('DROP TABLE vending_locations');
        $this->addSql('DROP TABLE vending_machines');
        $this->addSql('DROP TABLE vend_logs');
    }
}
