<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170802184534_populate_banks extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $banks = [
            [1, 'Natwest', '60-24-77', '19098596'],
            [2, 'TSB', '77-22-24', '13007568'],
        ];

        foreach ($banks as $bank) {
            [$id, $name, $sort_code, $account_number] = $bank;
            $this->addSql(
                'INSERT INTO banks (id, name, sort_code, account_number) VALUES (\'' . $id . '\', \'' . $name . '\', \'' . $sort_code . '\', \'' . $account_number . '\')'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'DELETE FROM banks WHERE id IN (0, 1, 2)'
        );
    }
}
