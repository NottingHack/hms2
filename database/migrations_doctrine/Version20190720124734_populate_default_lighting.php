<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190720124734_populate_default_lighting extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');

        $this->addSql(
            "INSERT INTO buildings (id, name) VALUES (1, 'Roden House')"
        );
        $this->addSql(
            "INSERT INTO floors (id, name, level, building_id) VALUES (1, 'HS2.0', 1, 1), (2, 'HS2.5', 0, 1)"
        );
        $this->addSql(
            "INSERT INTO rooms (id, name, floor_id) VALUES (1, 'CNC room', 2), (2, 'Blue room', 2), (3, 'Team storage', 2), (4, 'Members storage', 2), (5, 'Class room', 2), (6, 'Metal working', 2), (7, 'Disabled toilet', 2), (8, 'CNC room corridor', 2), (9, 'Corridor', 2), (10, 'Airlock', 1), (11, 'Kitchen', 1), (12, 'Toilets', 1), (13, 'Studio', 1), (14, 'Comfy area', 1), (15, 'Members storage', 1), (16, 'Craft room', 1), (17, 'Electronics', 1), (18, 'Dusty area', 1), (19, 'Wood working', 1), (20, 'Project storage', 1)"
        );
        $this->addSql(
            "INSERT INTO lights (id, name, room_id, output_channel_id) VALUES (1, 'Blue corridor', 2, 1), (2, 'Blue corridor', 2, 2), (3, 'Blue corridor', 2, 3), (4, '', 2, 4), (5, '', 2, 5), (6, '', 2, 6), (7, '', 2, 7), (8, '', 2, 8), (9, '', 2, 9), (10, 'CNC corridor', 1, 10), (11, 'CNC corridor', 1, 11), (12, '', 1, 12), (13, '', 1, 13), (14, '', 1, 14), (15, '', 1, 15), (16, 'CNC corridor', 8, 16), (17, 'CNC corridor', 8, 17), (18, '', 3, 18), (19, '', 3, 19), (20, 'Class Room', 5, 58), (21, 'Class Room', 5, 57), (22, 'Class Room', 5, 56), (23, 'Class Room', 5, 55), (24, 'Class Room', 5, 54), (25, 'Class Room', 5, 53), (26, 'Class Room', 5, 52), (27, 'Class Room', 5, 51), (28, 'Class Room', 5, 50), (29, 'Class Room', 5, 49), (30, 'Member Storage', 4, 65), (31, 'Member Storage', 4, 64), (32, 'Disabled toilet', 7, 25), (33, 'Corridor', 9, 63), (34, 'Corridor', 9, 62), (35, 'Corridor', 9, 61), (36, 'Corridor', 9, 60), (37, 'Corridor', 9, 59)"
        );
        $this->addSql(
            "INSERT INTO lighting_controllers (id, name, room_id) VALUES (1, 'CNCRoomController', 1), (2, 'MetalController', 6), (3, 'ClassRoomController', 5)"
        );
        $this->addSql(
            'INSERT INTO lighting_input_channels (id, channel, controller_id, pattern_id, statefull) VALUES (1, 1, 1, NULL, 0), (2, 2, 1, 5, 1), (3, 3, 1, NULL, 0), (4, 4, 1, NULL, 0), (5, 5, 1, NULL, 0), (6, 6, 1, NULL, 0), (7, 7, 1, NULL, 0), (8, 0, 1, NULL, 0), (9, 1, 2, NULL, 0), (10, 2, 2, NULL, 0), (11, 3, 2, NULL, 0), (12, 4, 2, NULL, 0), (13, 5, 2, NULL, 0), (14, 6, 2, NULL, 0), (15, 7, 2, NULL, 0), (16, 0, 2, NULL, 0), (17, 1, 3, 133, 1), (18, 2, 3, 127, 1), (19, 3, 3, NULL, 0), (20, 4, 3, NULL, 0), (21, 5, 3, NULL, 0), (22, 6, 3, NULL, 0), (23, 7, 3, NULL, 0), (24, 0, 3, NULL, 0)'
        );
        $this->addSql(
            'INSERT INTO lighting_output_channels (id, channel, controller_id) VALUES (1, 0, 1), (2, 1, 1), (3, 2, 1), (4, 3, 1), (5, 4, 1), (6, 5, 1), (7, 6, 1), (8, 7, 1), (9, 8, 1), (10, 9, 1), (11, 10, 1), (12, 11, 1), (13, 12, 1), (14, 13, 1), (15, 14, 1), (16, 15, 1), (17, 16, 1), (18, 17, 1), (19, 18, 1), (20, 19, 1), (21, 20, 1), (22, 21, 1), (23, 22, 1), (24, 23, 1), (25, 0, 2), (26, 1, 2), (27, 2, 2), (28, 3, 2), (29, 4, 2), (30, 5, 2), (31, 6, 2), (32, 7, 2), (33, 8, 2), (34, 9, 2), (35, 10, 2), (36, 11, 2), (37, 12, 2), (38, 13, 2), (39, 14, 2), (40, 15, 2), (41, 16, 2), (42, 17, 2), (43, 18, 2), (44, 19, 2), (45, 20, 2), (46, 21, 2), (47, 22, 2), (48, 23, 2), (49, 0, 3), (50, 1, 3), (51, 2, 3), (52, 3, 3), (53, 4, 3), (54, 5, 3), (55, 6, 3), (56, 7, 3), (57, 8, 3), (58, 9, 3), (59, 10, 3), (60, 11, 3), (61, 12, 3), (62, 13, 3), (63, 14, 3), (64, 15, 3), (65, 16, 3), (66, 17, 3), (67, 18, 3), (68, 19, 3), (69, 20, 3), (70, 21, 3), (71, 22, 3), (72, 23, 3)'
        );
        $this->addSql(
            "INSERT INTO lighting_patterns (id, name, next_pattern_id, timeout) VALUES (1, 'All Off', NULL, NULL), (2, 'All On', NULL, NULL), (3, 'Blue Room On', NULL, NULL), (4, 'CNC Room On', NULL, NULL), (5, 'Team Storage On', NULL, NULL), (6, 'G4/G5 Corridor On', NULL, NULL), (26, 'Blue Room Off', NULL, NULL), (32, 'CNC Room Off', NULL, NULL), (36, 'Flash CNC (Start)', 39, 500), (39, 'Flash CNC 1', 42, 200), (42, 'Flash CNC 2', 45, 500), (45, 'Flash CNC 3', 46, 200), (46, 'Flash CNC 4', 47, 500), (47, 'Flash CNC 5', 48, 200), (48, 'Flash CNC 6', 49, 500), (49, 'Flash CNC 7', 50, 200), (50, 'Flash CNC 8', 51, 500), (51, 'Flash CNC 9', 52, 200), (52, 'Flash CNC 10', 53, 500), (53, 'Flash CNC 11', 54, 200), (54, 'Flash CNC 12', 55, 500), (55, 'Flash CNC 13', NULL, NULL), (56, 'Flash Blue (Start)', 57, 500), (57, 'Flash Blue 1', 58, 200), (58, 'Flash Blue 2', 59, 500), (59, 'Flash Blue 3', 60, 200), (60, 'Flash Blue 4', 61, 500), (61, 'Flash Blue 5', 62, 200), (62, 'Flash Blue 6', 63, 500), (63, 'Flash Blue 7', 64, 200), (64, 'Flash Blue 8', 65, 500), (65, 'Flash Blue 9', 66, 200), (66, 'Flash Blue 10', 67, 500), (67, 'Flash Blue 11', 68, 200), (68, 'Flash Blue 12', 69, 500), (69, 'Flash Blue 13', NULL, NULL), (90, 'Roll 1', NULL, NULL), (93, 'Roll 2', NULL, NULL), (96, 'Roll 3', NULL, NULL), (99, 'Roll 4', NULL, NULL), (102, 'Roll 5', NULL, NULL), (105, 'Roll 6', NULL, NULL), (108, 'Roll Clear', NULL, NULL), (121, 'Corridor On', NULL, NULL), (124, 'Corridor Off', NULL, NULL), (127, 'Members Storage On', NULL, NULL), (130, 'Members Storage Off', NULL, NULL), (133, 'Class Room On', NULL, NULL), (136, 'Class Room Off', NULL, NULL)"
        );
        $this->addSql(
            "INSERT INTO light_lighting_pattern (pattern_id, light_id, state) VALUES (1, 1, 'OFF'), (1, 2, 'OFF'), (1, 3, 'OFF'), (1, 4, 'OFF'), (1, 5, 'OFF'), (1, 6, 'OFF'), (1, 7, 'OFF'), (1, 8, 'OFF'), (1, 9, 'OFF'), (1, 10, 'OFF'), (1, 11, 'OFF'), (1, 12, 'OFF'), (1, 13, 'OFF'), (1, 14, 'OFF'), (1, 15, 'OFF'), (1, 16, 'OFF'), (1, 17, 'OFF'), (1, 18, 'OFF'), (1, 19, 'OFF'), (1, 20, 'OFF'), (1, 21, 'OFF'), (1, 22, 'OFF'), (1, 23, 'OFF'), (1, 24, 'OFF'), (1, 25, 'OFF'), (1, 26, 'OFF'), (1, 27, 'OFF'), (1, 28, 'OFF'), (1, 29, 'OFF'), (1, 30, 'OFF'), (1, 31, 'OFF'), (1, 32, 'OFF'), (2, 1, 'ON'), (2, 2, 'ON'), (2, 3, 'ON'), (2, 4, 'ON'), (2, 5, 'ON'), (2, 6, 'ON'), (2, 7, 'ON'), (2, 8, 'ON'), (2, 9, 'ON'), (2, 10, 'ON'), (2, 11, 'ON'), (2, 12, 'ON'), (2, 13, 'ON'), (2, 14, 'ON'), (2, 15, 'ON'), (2, 16, 'ON'), (2, 17, 'ON'), (2, 18, 'ON'), (2, 19, 'ON'), (2, 20, 'ON'), (2, 21, 'ON'), (2, 22, 'ON'), (2, 23, 'ON'), (2, 24, 'ON'), (2, 25, 'ON'), (2, 26, 'ON'), (2, 27, 'ON'), (2, 28, 'ON'), (2, 29, 'ON'), (2, 30, 'ON'), (2, 31, 'ON'), (2, 32, 'ON'), (3, 1, 'ON'), (3, 2, 'ON'), (3, 3, 'ON'), (3, 4, 'ON'), (3, 5, 'ON'), (3, 6, 'ON'), (3, 7, 'ON'), (3, 8, 'ON'), (3, 9, 'ON'), (4, 12, 'ON'), (4, 13, 'ON'), (4, 14, 'ON'), (4, 15, 'ON'), (5, 18, 'ON'), (5, 19, 'ON'), (6, 1, 'ON'), (6, 2, 'ON'), (6, 3, 'ON'), (6, 10, 'ON'), (6, 11, 'ON'), (6, 16, 'ON'), (6, 17, 'ON'), (26, 1, 'OFF'), (26, 2, 'OFF'), (26, 3, 'OFF'), (26, 4, 'OFF'), (26, 5, 'OFF'), (26, 6, 'OFF'), (26, 7, 'OFF'), (26, 8, 'OFF'), (26, 9, 'OFF'), (32, 12, 'OFF'), (32, 13, 'OFF'), (32, 14, 'OFF'), (32, 15, 'OFF'), (36, 3, 'TOGGLE'), (39, 3, 'TOGGLE'), (42, 2, 'TOGGLE'), (45, 2, 'TOGGLE'), (46, 1, 'TOGGLE'), (47, 1, 'TOGGLE'), (48, 11, 'TOGGLE'), (49, 11, 'TOGGLE'), (50, 10, 'TOGGLE'), (51, 10, 'TOGGLE'), (52, 17, 'TOGGLE'), (53, 17, 'TOGGLE'), (54, 16, 'TOGGLE'), (55, 16, 'TOGGLE'), (56, 16, 'TOGGLE'), (57, 16, 'TOGGLE'), (58, 17, 'TOGGLE'), (59, 17, 'TOGGLE'), (60, 10, 'TOGGLE'), (61, 10, 'TOGGLE'), (62, 11, 'TOGGLE'), (63, 11, 'TOGGLE'), (64, 1, 'TOGGLE'), (65, 1, 'TOGGLE'), (66, 2, 'TOGGLE'), (67, 2, 'TOGGLE'), (68, 3, 'TOGGLE'), (69, 3, 'TOGGLE'), (90, 1, 'OFF'), (90, 2, 'OFF'), (90, 3, 'OFF'), (90, 4, 'OFF'), (90, 5, 'ON'), (90, 6, 'OFF'), (90, 7, 'OFF'), (90, 8, 'OFF'), (90, 9, 'OFF'), (93, 1, 'OFF'), (93, 2, 'OFF'), (93, 3, 'ON'), (93, 4, 'OFF'), (93, 5, 'OFF'), (93, 6, 'OFF'), (93, 7, 'ON'), (93, 8, 'OFF'), (93, 9, 'OFF'), (96, 1, 'ON'), (96, 2, 'OFF'), (96, 3, 'OFF'), (96, 4, 'OFF'), (96, 5, 'ON'), (96, 6, 'OFF'), (96, 7, 'OFF'), (96, 8, 'OFF'), (96, 9, 'ON'), (99, 1, 'ON'), (99, 2, 'OFF'), (99, 3, 'ON'), (99, 4, 'OFF'), (99, 5, 'OFF'), (99, 6, 'OFF'), (99, 7, 'ON'), (99, 8, 'OFF'), (99, 9, 'ON'), (102, 1, 'ON'), (102, 2, 'OFF'), (102, 3, 'ON'), (102, 4, 'OFF'), (102, 5, 'ON'), (102, 6, 'OFF'), (102, 7, 'ON'), (102, 8, 'OFF'), (102, 9, 'ON'), (105, 1, 'ON'), (105, 2, 'OFF'), (105, 3, 'ON'), (105, 4, 'ON'), (105, 5, 'OFF'), (105, 6, 'ON'), (105, 7, 'ON'), (105, 8, 'OFF'), (105, 9, 'ON'), (108, 1, 'OFF'), (108, 2, 'OFF'), (108, 3, 'OFF'), (108, 4, 'OFF'), (108, 5, 'OFF'), (108, 6, 'OFF'), (108, 7, 'OFF'), (108, 8, 'OFF'), (108, 9, 'OFF'), (121, 33, 'ON'), (121, 34, 'ON'), (121, 35, 'ON'), (121, 36, 'ON'), (121, 37, 'ON'), (124, 33, 'OFF'), (124, 34, 'OFF'), (124, 35, 'OFF'), (124, 36, 'OFF'), (124, 37, 'OFF'), (127, 30, 'ON'), (127, 31, 'ON'), (130, 30, 'OFF'), (130, 31, 'OFF'), (133, 20, 'ON'), (133, 21, 'ON'), (133, 22, 'ON'), (133, 23, 'ON'), (133, 24, 'ON'), (133, 25, 'ON'), (133, 26, 'ON'), (133, 27, 'ON'), (133, 28, 'ON'), (133, 29, 'ON'), (136, 20, 'OFF'), (136, 21, 'OFF'), (136, 22, 'OFF'), (136, 23, 'OFF'), (136, 24, 'OFF'), (136, 25, 'OFF'), (136, 26, 'OFF'), (136, 27, 'OFF'), (136, 28, 'OFF'), (136, 29, 'OFF')"
        );

        $this->addSql('SET foreign_key_checks = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql('TRUNCATE TABLE light_lighting_pattern');
        $this->addSql('TRUNCATE TABLE lighting_patterns');
        $this->addSql('TRUNCATE TABLE lighting_output_channels');
        $this->addSql('TRUNCATE TABLE lighting_input_channels');
        $this->addSql('TRUNCATE TABLE lighting_controllers');
        $this->addSql('TRUNCATE TABLE lights');
        $this->addSql('TRUNCATE TABLE floors');
        $this->addSql('TRUNCATE TABLE buildings');

        $this->addSql('SET foreign_key_checks = 1');
    }
}
