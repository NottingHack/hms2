<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170512090722_add_role_update_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role_updates (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, added_role_id INT UNSIGNED DEFAULT NULL, removed_role_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F985B6CBA76ED395 (user_id), INDEX IDX_F985B6CBE660F4B0 (added_role_id), INDEX IDX_F985B6CB85D15B53 (removed_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F820A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F820E660F4B0 FOREIGN KEY (added_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F82085D15B53 FOREIGN KEY (removed_role_id) REFERENCES roles (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE role_updates');
    }
}
