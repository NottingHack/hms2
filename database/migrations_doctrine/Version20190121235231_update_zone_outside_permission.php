<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190121235231_update_zone_outside_permission extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            "UPDATE zones SET permission_code = 'gatekeeper.zoneEntry.outside' WHERE id = 1"
        );

        $this->addSql(
            "UPDATE doors SET side_a_zone_id = 1, side_b_zone_id = 1 WHERE short_name = 'UP-OUTER'"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'UPDATE zones SET permission_code = NULL WHERE id = 1'
        );

        $this->addSql(
            "UPDATE doors SET side_a_zone_id = NULL, side_b_zone_id = NULL WHERE short_name = 'UP-OUTER'"
        );
    }
}
