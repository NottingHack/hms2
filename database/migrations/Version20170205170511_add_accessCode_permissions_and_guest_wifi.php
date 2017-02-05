<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170205170511 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $now = Carbon::now();
        $settings = [
            'access_guest_wifi_ssid' => 'HSNOTTS-guest',
            'access_guest_wifi_password' => '123456',
        ];

        foreach ($settings as $key => $value) {
            $this->addSql(
                'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\''.$key.'\', \''.$value.'\', null, \''.$now.'\', \''.$now.'\')'
            );
        }

        $this->addSql('INSERT INTO permissions (name) VALUES (\'accessCodes.view\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM permissions WHERE name = \'accessCodes.view\'');

        $this->addSql('DELETE FROM meta WHERE key = \'access_guest_wifi_ssid\'');
        $this->addSql('DELETE FROM meta WHERE key = \'access_guest_wifi_password\'');
    }
}
