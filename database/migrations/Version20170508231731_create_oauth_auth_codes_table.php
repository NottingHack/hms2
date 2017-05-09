<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170508231731_create_oauth_auth_codes_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE oauth_auth_codes (
          id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          user_id int(11) NOT NULL,
          client_id int(11) NOT NULL,
          scopes text COLLATE utf8_unicode_ci,
          revoked tinyint(1) NOT NULL,
          expires_at datetime DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE oauth_auth_codes');
    }
}
