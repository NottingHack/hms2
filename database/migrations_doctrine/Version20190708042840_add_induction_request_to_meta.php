<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190708042840_add_induction_request_to_meta extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $now = Carbon::now();

        $this->addSql(
            'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'induction_request_html\', \'https://goo.gl/Jl59IM\', null, \'' . $now . '\', \'' . $now . '\')'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'DELETE FROM meta WHERE `key` = \'induction_request_html\''
        );
    }
}
