<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170508231756_create_oauth_clients_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_clients (
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          user_id int(11) DEFAULT NULL,
          name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          secret varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          redirect text COLLATE utf8_unicode_ci NOT NULL,
          personal_access_client tinyint(1) NOT NULL,
          password_client tinyint(1) NOT NULL,
          revoked tinyint(1) NOT NULL,
          created_at timestamp NULL DEFAULT NULL,
          updated_at timestamp NULL DEFAULT NULL,
          PRIMARY KEY (id),
          KEY oauth_clients_user_id_index (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oauth_clients');
    }
}
