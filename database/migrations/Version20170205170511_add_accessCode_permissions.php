<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170205170511_add_accessCode_permissions extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO permissions (name) VALUES (\'accessCodes.view\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM permissions WHERE name = \'accessCodes.view\'');
    }
}
