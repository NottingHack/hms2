<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170206225300_fix_invite_index extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX uniq_c7e210d7e7927c74 ON invites');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_37E6A6CE7927C74 ON invites (email)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX uniq_37e6a6ce7927c74 ON invites');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7E210D7E7927C74 ON invites (email)');
    }
}
