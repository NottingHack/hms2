<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version1_20161023_2__add_member_roles_and_permissions extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO roles VALUES (1, \'awaitingApproval\')');
        $this->addSql('INSERT INTO roles VALUES (2, \'awaitingPayment\')');
        $this->addSql('INSERT INTO roles VALUES (3, \'youngMember\')');
        $this->addSql('INSERT INTO roles VALUES (4, \'currentMember\')');
        $this->addSql('INSERT INTO roles VALUES (5, \'exMember\')');
        $this->addSql('INSERT INTO permissions VALUES (1, \'view.self\')');
        $this->addSql('INSERT INTO permission_role VALUES (1, 1), (2, 1), (3, 1), (4, 1), (5, 1)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // don't need to delete from permission_role table as this is taken care of by the cascade
        $this->addSql('DELETE FROM permissions WHERE id IN (1)');
        $this->addSql('DELETE FROM roles WHERE id IN (1,2,3,4,5)');
    }
}
