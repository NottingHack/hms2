<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170502204811_add_user_account_relation extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD acccount_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986C91FE5 FOREIGN KEY (acccount_id) REFERENCES accounts (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64986C91FE5 ON user (acccount_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986C91FE5');
        $this->addSql('DROP INDEX IDX_8D93D64986C91FE5 ON user');
        $this->addSql('ALTER TABLE user DROP acccount_id');
    }
}
