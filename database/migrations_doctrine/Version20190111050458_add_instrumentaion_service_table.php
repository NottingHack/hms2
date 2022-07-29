<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111050458_add_instrumentaion_service_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE service_status (service_name VARCHAR(100) NOT NULL, status INT NOT NULL, status_str VARCHAR(255) DEFAULT NULL, query_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, reply_time DATETIME DEFAULT NULL, restart_time DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(service_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE service_status');
    }
}
