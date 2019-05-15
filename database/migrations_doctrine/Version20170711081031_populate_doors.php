<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170711081031_populate_doors extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $now = Carbon::now();
        $doors = [
            [1, 'Upstairs inner Gatekeeper door', 'UP-INNER', 'LOCKED', $now, 1],
            [2, 'Upstairs outer door', 'UP-OUTER', 'UNKNOWN', $now, 'null'],
            [3, 'Workshop door', 'WORKSHOP', 'UNKNOWN', $now, 'null'],
        ];

        foreach ($doors as $door) {
            [$id, $description, $short_name, $state, $state_change, $side_a_zone_id] = $door;
            $this->addSql(
                'INSERT INTO doors (id, description, short_name, state, state_change, side_a_zone_id, permission_code) VALUES (\'' . $id . '\', \'' . $description . '\', \'' . $short_name . '\', \'' . $state . '\', \'' . $state_change . '\', ' . $side_a_zone_id . ', null)'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'DELETE FROM doors WHERE id IN (1, 2, 3)'
        );
    }
}
