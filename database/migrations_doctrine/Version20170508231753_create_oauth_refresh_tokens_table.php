<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170508231753_create_oauth_refresh_tokens_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_refresh_tokens (
          id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          access_token_id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          revoked tinyint(1) NOT NULL,
          expires_at datetime DEFAULT NULL,
          PRIMARY KEY (id),
          KEY oauth_refresh_tokens_access_token_id_index (access_token_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oauth_refresh_tokens');
    }
}
