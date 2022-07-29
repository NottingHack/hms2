<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111055415_add_tool_usage_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tool_usages (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, tool_id INT UNSIGNED DEFAULT NULL, start DATETIME DEFAULT NULL, duration INT DEFAULT NULL, active_time INT DEFAULT NULL, status VARCHAR(20) DEFAULT NULL, INDEX IDX_2E8A5975A76ED395 (user_id), INDEX IDX_2E8A59758F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tool_usages ADD CONSTRAINT FK_2E8A5975A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tool_usages ADD CONSTRAINT FK_2E8A59758F7B22CC FOREIGN KEY (tool_id) REFERENCES tools (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tool_usages');
    }
}
