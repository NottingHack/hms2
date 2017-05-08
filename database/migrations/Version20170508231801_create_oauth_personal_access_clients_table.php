<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170508231801_create_oauth_personal_access_clients_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_personal_access_clients (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          client_id int(11) NOT NULL,
          created_at timestamp NULL DEFAULT NULL,
          updated_at timestamp NULL DEFAULT NULL,
          PRIMARY KEY (id),
          KEY oauth_personal_access_clients_client_id_index (client_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oauth_personal_access_clients');
    }
}
