<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190119205657_updated_populated_zones extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // for some reason I messed with these in Version20190111021836_populate_bells_and_door_bell
        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql('UPDATE zones SET id = id+1 ORDER BY ID DESC');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id+1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id+1 WHERE side_b_zone_id IS NOT NULL');
        $this->addSql('SET foreign_key_checks = 1');

        // add some new zones
        $zones = [
            [4, 'Downstairs Team storage', 'Zone 3', 'gatekeeper.zoneEntry.teamStrorage'],
            [5, 'Downstairs Member storage', 'Zone 4', 'gatekeeper.zoneEntry.downstairsMembersStrorage'],
            [6, 'Upstairs / HS2.0', 'Zone 5', 'gatekeeper.zoneEntry.upstairs'],
        ];

        foreach ($zones as $zone) {
            [$id, $description, $short_name, $permission_code] = $zone;
            $this->addSql(
                "INSERT INTO zones (id, description, short_name, permission_code) VALUES ($id, '$description', '$short_name', '$permission_code')"
            );
        }

        $this->addSql(
            'UPDATE zones SET permission_code = NULL WHERE id = 1'
        );

        $this->addSql(
            "UPDATE zones SET permission_code = 'gatekeeper.zoneEntry.classRoomMetalworking' WHERE id = 2"
        );

        $this->addSql(
            "UPDATE zones SET description = 'CNC & blue room', permission_code = 'gatekeeper.zoneEntry.cncBlueRoom' WHERE id = 3"
        );

        $this->addSql(
            "UPDATE doors SET side_a_zone_id = 1, side_b_zone_id = 6 WHERE short_name = 'WORKSHOP'"
        );

        $this->addSql(
            "UPDATE doors SET side_b_zone_id = 6 WHERE short_name = 'UP-INNER'"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            "UPDATE doors SET side_a_zone_id = NULL, side_b_zone_id = NULL WHERE short_name = 'WORKSHOP'"
        );

        $this->addSql(
            "UPDATE doors SET side_b_zone_id = NULL WHERE short_name = 'UP-INNER'"
        );

        $this->addSql(
            "UPDATE zones SET permission_code = 'ZONE1' WHERE id = 2"
        );

        $this->addSql(
            "UPDATE zones SET description = 'Laser, CNC & blue room', permission_code = 'ZONE2' WHERE id = 3"
        );

        $this->addSql(
            "UPDATE zones SET permission_code = '' WHERE id = 1"
        );

        $this->addSql(
            'DELETE FROM zones WHERE id IN (4, 5, 6)'
        );

        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql('UPDATE zones SET id = id-1');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id-1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id-1 WHERE side_b_zone_id IS NOT NULL');
        $this->addSql('SET foreign_key_checks = 1');
    }
}
