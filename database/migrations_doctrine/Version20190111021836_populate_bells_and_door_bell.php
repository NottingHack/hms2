<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111021836_populate_bells_and_door_bell extends AbstractMigration
{
    protected $doors = [
        [4, 'CNC corridor door', 'CNCCORIDOR', 'LOCKED', 0, 2],
        [6, 'Communal (Left)', 'COMMUNAL-L', 'LOCKED', 0, 2],
    ];

    protected $bells = [
        [1, 'Comfy area - 1 ring ', 'nh/gk/bell/ComfyArea', '1', 0],
        [2, 'Comfy area - 2 rings', 'nh/gk/bell/ComfyArea', '2', 1],
        [3, 'Comfy area - 3 rings', 'nh/gk/bell/ComfyArea', '3', 1],
        [4, 'Workshop - 1 ring ', 'nh/gk/bell/Workshop', '1', 1],
        [5, 'Workshop - 2 rings', 'nh/gk/bell/Workshop', '2', 1],
        [6, 'Workshop - 3 rings', 'nh/gk/bell/Workshop', '3', 1],
        [7, 'G5 - 1 ring', 'nh/gk/bell/G5', '1', 1],
        [8, 'G5 - 2 rings', 'nh/gk/bell/G5', '2', 1],
    ];

    protected $door_bell = [
        [1, 1],
        [1, 4],
        [2, 2],
        [2, 5],
        [3, 6],
        [4, 7],
        [6, 8],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql('UPDATE zones SET id = id-1');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id-1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id-1 WHERE side_b_zone_id IS NOT NULL');

        foreach ($this->doors as [$id, $description, $short_name, $state, $side_a_zone_id, $side_b_zone_id]) {
            $this->addSql(
                "INSERT INTO doors (id, description, short_name, state, state_change, side_a_zone_id, side_b_zone_id) VALUES ($id, '$description', '$short_name', '$state', NOW(), $side_a_zone_id, $side_b_zone_id)"
            );
        }

        foreach ($this->bells as [$id, $description, $topic, $message, $enabled]) {
            $this->addSql(
                "INSERT INTO bells (id, description, topic, message, enabled) VALUES ('$id', '$description', '$topic', '$message', '$enabled')"
            );
        }

        foreach ($this->door_bell as [$door_id, $bell_id]) {
            $this->addSql("INSERT INTO door_bell (door_id, bell_id) VALUES ($door_id, $bell_id)");
        }

        $this->addSql('SET foreign_key_checks = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        foreach ($this->door_bell as [$door_id, $bell_id]) {
            $this->addSql(
                "DELETE FROM door_bell WHERE door_id = $door_id AND bell_id = $bell_id"
            );
        }

        foreach ($this->bells as [$id, $description, $topic, $message, $enabled]) {
            $this->addSql(
                "DELETE FROM bells WHERE id = $id"
            );
        }

        foreach ($this->doors as [$id, $description, $short_name, $state, $side_a_zone_id, $side_b_zone_id]) {
            $this->addSql(
                "DELETE FROM doors WHERE id = $id"
            );
        }

        $this->addSql('UPDATE zones SET id = id+1 ORDER BY ID DESC');
        $this->addSql('UPDATE doors SET side_a_zone_id = side_a_zone_id+1 WHERE side_a_zone_id IS NOT NULL');
        $this->addSql('UPDATE doors SET side_b_zone_id = side_b_zone_id+1 WHERE side_b_zone_id IS NOT NULL');

        $this->addSql('SET foreign_key_checks = 1');
    }
}
