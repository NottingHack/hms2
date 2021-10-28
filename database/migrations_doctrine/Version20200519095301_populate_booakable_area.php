<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200519095301_populate_booakable_area extends AbstractMigration
{
    protected $areas = [
        ['Woodworking', 'General Workshop area for Woodworking and Dusty', 'indigo', 1],
        ['Metalworking', 'Metalworking areas, both upstairs and down', 'yellow', 1],
        ['Laser', 'Laser area for either A0 or A2', 'pink', 1],
        ['Craft', 'Craft room. Also some studio spill out for larger things like LARP costume work', 'orange', 1],
        ['CNC / Electronics', 'CNC / Electronics room downstairs', 'cyan', 1],
        ['Classroom', 'Downstairs classroom', 'red', 0],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $now = Carbon::now();

        foreach ($this->areas as [$name, $description, $booking_color, $self_bookable]) {
            $this->addSql(
                'INSERT INTO bookable_areas (name, building_id, description, booking_color, self_bookable, created_at, updated_at) '
                . 'VALUES (\'' . $name . '\', 1, \'' . $description . '\', \'' . $booking_color . '\', \'' . $self_bookable . '\', \'' . $now . '\', \'' . $now . '\')'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        foreach ($this->areas as [$name, $description, $booking_color, $self_bookable]) {
            $this->addSql(
                'DELETE FROM bookable_areas WHERE `name` = \'' . $name . '\''
            );
        }
    }
}
