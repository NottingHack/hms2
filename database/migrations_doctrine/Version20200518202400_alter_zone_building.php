<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200518202400_alter_zone_building extends AbstractMigration
{
    protected $building_zone = [
        [1, 1],
        [1, 2],
        [1, 3],
        [1, 4],
        [1, 5],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE zones ADD building_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zones ADD CONSTRAINT FK_85CAB1684D2A7E12 FOREIGN KEY (building_id) REFERENCES buildings (id)');
        $this->addSql('CREATE INDEX IDX_85CAB1684D2A7E12 ON zones (building_id)');

        foreach ($this->building_zone as [$building_id, $zone_id]) {
            $this->addSql("UPDATE zones SET building_id = $building_id WHERE id = $zone_id");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE zones DROP FOREIGN KEY FK_85CAB1684D2A7E12');
        $this->addSql('DROP INDEX IDX_85CAB1684D2A7E12 ON zones');
        $this->addSql('ALTER TABLE zones DROP building_id');
    }
}
