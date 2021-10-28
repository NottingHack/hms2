<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190110025754_meta_so_bank_id extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $now = Carbon::now();

        $this->addSql(
            'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'so_bank_id\', \'2\', null, \'' . $now . '\', \'' . $now . '\')'
        );
        $this->addSql('DELETE FROM meta WHERE `key` = \'so_accountName\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $now = Carbon::now();

        $this->addSql(
            'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'so_accountName\', \'Nottingham Hackspace Ltd\', null, \'' . $now . '\', \'' . $now . '\')'
        );
        $this->addSql('DELETE FROM meta WHERE `key` = \'so_bank_id\'');
    }
}
