<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190113134517_populate_vending_machines_and_locations extends AbstractMigration
{
    protected $vendingMachines = [
        [1, 'Studio vending machine', 'VEND', 'UDP', '192.168.0.12'],
        [2, 'Note acceptor', 'NOTE', 'MQTT', 'nh/note_acc/'],
        [3, 'Coin acceptor', 'NOTE', 'MQTT', 'nh/coin_acc/'],
        [4, 'Can vending machine', 'VEND', 'MQTT', 'nh/can_vend/'],
    ];

    protected $vendingLocations = [
        [1, 1, '41-31', 'A1'],
        [2, 1, '41-33', 'A3'],
        [3, 1, '41-35', 'A5'],
        [4, 1, '41-37', 'A7'],
        [5, 1, '42-31', 'B1'],
        [6, 1, '42-33', 'B3'],
        [7, 1, '42-35', 'B5'],
        [8, 1, '42-37', 'B7'],
        [9, 1, '43-31', 'C1'],
        [10, 1, '43-33', 'C3'],
        [11, 1, '43-35', 'C5'],
        [12, 1, '43-37', 'C7'],
        [13, 1, '44-31', 'D1'],
        [14, 1, '44-33', 'D3'],
        [15, 1, '44-35', 'D5'],
        [16, 1, '44-37', 'D7'],
        [17, 1, '45-31', 'E1'],
        [18, 1, '45-32', 'E2'],
        [19, 1, '45-33', 'E3'],
        [20, 1, '45-34', 'E4'],
        [21, 1, '45-35', 'E5'],
        [22, 1, '45-36', 'E6'],
        [23, 1, '45-37', 'E7'],
        [24, 1, '45-38', 'E8'],
        [25, 1, '46-31', 'F1'],
        [26, 1, '46-32', 'F2'],
        [27, 1, '46-33', 'F3'],
        [28, 1, '46-34', 'F4'],
        [29, 1, '46-35', 'F5'],
        [30, 1, '46-36', 'F6'],
        [31, 1, '46-37', 'F7'],
        [36, 4, '00-00', 'A1'],
        [39, 4, '00-01', 'A2'],
        [42, 4, '00-02', 'A3'],
        [45, 4, '00-03', 'A4'],
        [48, 4, '00-04', 'A5'],
        [51, 4, '00-05', 'A6'],
        [54, 4, '00-06', 'A7'],
        [57, 4, '00-07', 'A8'],
        [60, 4, '00-08', 'A9'],
        [63, 4, '00-0a', 'B1'],
        [66, 4, '00-0b', 'B2'],
        [69, 4, '00-0c', 'B3'],
        [72, 4, '00-0d', 'B4'],
        [75, 4, '00-0e', 'B5'],
        [78, 4, '00-0f', 'B6'],
        [81, 4, '00-10', 'B7'],
        [84, 4, '00-11', 'B8'],
        [87, 4, '00-12', 'B9'],
        [90, 4, '00-14', 'C1'],
        [93, 4, '00-15', 'C2'],
        [96, 4, '00-16', 'C3'],
        [99, 4, '00-17', 'C4'],
        [102, 4, '00-18', 'C5'],
        [105, 4, '00-19', 'C6'],
        [108, 4, '00-1a', 'C7'],
        [111, 4, '00-1b', 'C8'],
        [114, 4, '00-1c', 'C9'],
        [117, 4, '00-1e', 'D1'],
        [120, 4, '00-1f', 'D2'],
        [123, 4, '00-20', 'D3'],
        [126, 4, '00-21', 'D4'],
        [129, 4, '00-22', 'D5'],
        [132, 4, '00-23', 'D6'],
        [135, 4, '00-24', 'D7'],
        [138, 4, '00-25', 'D8'],
        [141, 4, '00-26', 'D9'],
        [144, 4, '00-28', 'E1'],
        [147, 4, '00-29', 'E2'],
        [150, 4, '00-2a', 'E3'],
        [153, 4, '00-2b', 'E4'],
        [156, 4, '00-2c', 'E5'],
        [159, 4, '00-2d', 'E6'],
        [162, 4, '00-2e', 'E7'],
        [165, 4, '00-2f', 'E8'],
        [168, 4, '00-30', 'E9'],
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        foreach ($this->vendingMachines as [$id, $description, $type, $connection, $address]) {
            $this->addSql(
                "INSERT INTO vending_machines (id, description, type, connection, address) VALUES ($id, '$description', '$type', '$connection', '$address')"
            );
        }

        foreach ($this->vendingLocations as [$id, $vending_machine_id, $encoding, $name]) {
            $this->addSql(
                "INSERT INTO vending_locations (id, vending_machine_id, encoding, name) VALUE ($id, $vending_machine_id, '$encoding', '$name')"
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        foreach ($this->vendingLocations as [$id, $vending_machine_id, $encoding, $name]) {
            $this->addSql(
                "DELETE FROM vending_locations WHERE id = $id"
            );
        }

        foreach ($this->vendingMachines as [$id, $description, $type, $connection, $address]) {
            $this->addSql(
                "DELETE FROM vending_machines WHERE id = $id)"
            );
        }
    }
}
