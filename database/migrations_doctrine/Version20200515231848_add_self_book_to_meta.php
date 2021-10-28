<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200515231848_add_self_book_to_meta extends AbstractMigration
{
    protected $settings = [
        'self_book_max_length' => '180',
        'self_book_max_concurrent_per_user' => '1',
        'self_book_max_guests_per_user' => '1',
        'self_book_min_period_between_bookings' => '720',
        'self_book_info_text' => 'Based on current government guidelines your can only bring guest from you household into the space!',
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
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
    public function down(Schema $schema): void
    {
        foreach ($this->settings as $key => $value) {
            $this->addSql(
                'DELETE FROM meta WHERE `key` = \'' . $key . '\''
            );
        }
    }
}
