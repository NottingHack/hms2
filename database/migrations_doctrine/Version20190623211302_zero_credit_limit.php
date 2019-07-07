<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190623211302_zero_credit_limit extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'UPDATE meta SET `value` = 0 WHERE `key` = \'member_credit_limit\''
        );

        $this->addSql(
            'UPDATE profile SET credit_limit = 0 WHERE credit_limit = 2000'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'UPDATE meta SET `value` = 2000 WHERE `key` = \'member_credit_limit\''
        );

        $this->addSql(
            'UPDATE profile SET credit_limit = 2000 WHERE credit_limit = 0'
        );
    }
}
