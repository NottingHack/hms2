<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190708220136_add_zone_occupant_reset_interval_to_meta extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $now = Carbon::now();

        $this->addSql(
            'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'zone_occupant_reset_interval\', \'P1D\', null, \'' . $now . '\', \'' . $now . '\')'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'DELETE FROM meta WHERE `key` = \'zone_occupant_reset_interval\''
        );
    }
}
