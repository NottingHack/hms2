<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210227204944_populate_allow_cash_membership_to_meta extends AbstractMigration
{
    protected $settings = [
        'allow_cash_membership_payments' => 0,
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $now = Carbon::now();

        foreach ($this->settings as $key => $value) {
            $this->addSql(
                'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'' . $key . '\', \'' . $value . '\', null, \'' . $now . '\', \'' . $now . '\')'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        foreach ($this->settings as $key => $value) {
            $this->addSql(
                'DELETE FROM meta WHERE `key` = \'' . $key . '\''
            );
        }
    }
}
