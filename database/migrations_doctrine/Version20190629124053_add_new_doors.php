<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190629124053_add_new_doors extends AbstractMigration
{
    protected $doors = [
        [7, 'Communal (Right)', 'COMMUNAL-R', 'LOCKED', 0, 1],
        [8, 'Members storage', 'MEMBERSSTORE', 'LOCKED', 1, 4],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            "UPDATE zones SET permission_code = 'gatekeeper.zoneEntry.downstairsMembersStorage' WHERE short_name = 'Zone 4'"
        );

        foreach ($this->doors as [$id, $description, $short_name, $state, $side_a_zone_id, $side_b_zone_id]) {
            $this->addSql(
                "INSERT INTO doors (id, description, short_name, state, state_change, side_a_zone_id, side_b_zone_id) VALUES ($id, '$description', '$short_name', '$state', NOW(), $side_a_zone_id, $side_b_zone_id)"
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        foreach ($this->doors as [$id, $description, $short_name, $state, $side_a_zone_id, $side_b_zone_id]) {
            $this->addSql(
                "DELETE FROM doors WHERE id = $id"
            );
        }
    }
}
