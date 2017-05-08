<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170508231751_create_oauth_access_tokens_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_access_tokens (
          id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          user_id int(11) DEFAULT NULL,
          client_id int(11) NOT NULL,
          name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          scopes text COLLATE utf8_unicode_ci,
          revoked tinyint(1) NOT NULL,
          created_at timestamp NULL DEFAULT NULL,
          updated_at timestamp NULL DEFAULT NULL,
          expires_at datetime DEFAULT NULL,
          PRIMARY KEY (id),
          KEY oauth_access_tokens_user_id_index (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oauth_access_tokens');
    }
}
