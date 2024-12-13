<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241209032809_add_tools_transaction_type_override extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds transaction_type_override column to tools table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tools ADD transaction_type_override VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tools DROP transaction_type_override');
    }
}
