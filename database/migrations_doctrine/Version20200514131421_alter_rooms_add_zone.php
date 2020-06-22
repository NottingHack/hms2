<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200514131421_alter_rooms_add_zone extends AbstractMigration
{
    protected $room_zone = [
        [1, 2],
        [2, 2],
        [3, 3],
        [4, 4],
        [5, 1],
        [6, 1],
        [7, 1],
        [8, 2],
        [9, 1],
        [10, 0],
        [11, 5],
        [12, 5],
        [13, 5],
        [14, 5],
        [15, 5],
        [16, 5],
        [17, 5],
        [18, 5],
        [19, 5],
        [20, 5],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rooms ADD zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A969F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('CREATE INDEX IDX_7CA11A969F2C3FAB ON rooms (zone_id)');

        foreach ($this->room_zone as [$room_id, $zone_id]) {
            $this->addSql("UPDATE rooms SET zone_id = $zone_id WHERE id = $room_id");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A969F2C3FAB');
        $this->addSql('DROP INDEX IDX_7CA11A969F2C3FAB ON rooms');
        $this->addSql('ALTER TABLE rooms DROP zone_id');
    }
}
