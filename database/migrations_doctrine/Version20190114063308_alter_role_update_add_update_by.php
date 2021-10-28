<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190114063308_alter_role_update_add_update_by extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_updates ADD update_by_user_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_F985B6CB41EF462E FOREIGN KEY (update_by_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F985B6CB41EF462E ON role_updates (update_by_user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_F985B6CB41EF462E');
        $this->addSql('DROP INDEX IDX_F985B6CB41EF462E ON role_updates');
        $this->addSql('ALTER TABLE role_updates DROP update_by_user_id');
    }
}
