<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20161024103730 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO roles VALUES (1, \'member\')');
        $this->addSql('INSERT INTO permissions VALUES (1, \'view\'), (2, \'view.other\')');
        $this->addSql('INSERT INTO permission_role VALUES (1, 1), (1, 2)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM permissions_role WHERE id IN (1,2)');
        $this->addSql('DELETE FROM permissions WHERE id IN (1,2)');
        $this->addSql('DELETE FROM roles WHERE id = 1');
    }
}
