<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190622192125_update_starter_links extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $links = [
            ['Members Guide', 'https://guide.nottinghack.org.uk', 'The Hackspace Members guide'],
            ['Resources Request', 'https://docs.google.com/forms/d/1jURztPXwFxh3NbXU0oqdC7T3YfJYK-6x67lq79HAqR8/viewform', 'Resources request form'],
        ];

        foreach ($links as $link) {
            [$name, $url, $description] = $link;
            $this->addSql(
                'UPDATE links SET description = \'' . $description . '\' WHERE name = \'' . $name . '\''
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $links = [
            ['Members Guide', 'https://guide.nottinghack.org.uk', 'The Hackspace Memmbers guide'],
            ['Resources Request', 'https://docs.google.com/forms/d/1jURztPXwFxh3NbXU0oqdC7T3YfJYK-6x67lq79HAqR8/viewform', 'Resources requetst form'],
        ];

        foreach ($links as $link) {
            [$name, $url, $description] = $link;
            $this->addSql(
                'UPDATE links SET description = \'' . $description . '\' WHERE name = \'' . $name . '\''
            );
        }
    }
}
