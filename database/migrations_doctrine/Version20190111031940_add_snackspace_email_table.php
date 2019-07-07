<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111031940_add_snackspace_email_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE snackspace_emails (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, `to` VARCHAR(255) DEFAULT NULL, cc VARCHAR(255) DEFAULT NULL, bcc VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, body_alt LONGTEXT DEFAULT NULL, status VARCHAR(16) DEFAULT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, link INT DEFAULT NULL, INDEX IDX_43A9E3CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE snackspace_emails ADD CONSTRAINT FK_43A9E3CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE snackspace_emails');
    }
}
