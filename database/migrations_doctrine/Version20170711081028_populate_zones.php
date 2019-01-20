<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170711081028_populate_zones extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $zones = [
            [1, 'Off-site', 'Off-site', null],
            [2, 'Metalworking, classroom & bike storage', 'Zone 1', 'ZONE1'],
            [3, 'Laser, CNC & blue room', 'Zone 2', 'ZONE2'],
        ];

        foreach ($zones as $zone) {
            list($id, $description, $short_name, $permission_code) = $zone;
            $this->addSql(
                'INSERT INTO zones (id, description, short_name, permission_code) VALUES (\'' . $id . '\', \'' . $description . '\', \'' . $short_name . '\', \'' . $permission_code . '\')'
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
            'DELETE FROM zones WHERE id IN (1, 2, 3)'
        );
    }
}
