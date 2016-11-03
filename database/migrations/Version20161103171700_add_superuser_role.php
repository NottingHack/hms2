<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20161103171700_add_superuser_role extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO roles VALUES (6, \'user.super\')');
        $this->addSql('INSERT INTO permissions VALUES (2, \'profile.view.all\')');
        $this->addSql('INSERT INTO permissions VALUES (3, \'role.view.all\')');
        $this->addSql('INSERT INTO permissions VALUES (4, \'role.edit.all\')');

        $this->addSql('INSERT INTO permission_role VALUES (6, 1), (6, 2), (6, 3), (6, 4)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // don't need to delete from permission_role table as this is taken care of by the cascade
        $this->addSql('DELETE FROM permissions WHERE id IN (2,3,4)');
        $this->addSql('DELETE FROM roles WHERE id = 6');
    }
}
