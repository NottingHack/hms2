<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20191227014410_add_proxy_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE proxies (id INT UNSIGNED AUTO_INCREMENT NOT NULL, meeting_id INT UNSIGNED DEFAULT NULL, principal_id INT UNSIGNED DEFAULT NULL, proxy_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_148784BB67433D9C (meeting_id), INDEX IDX_148784BB474870EE (principal_id), INDEX IDX_148784BBDB26A4E (proxy_id), UNIQUE INDEX duplicate_principal_idx (meeting_id, principal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proxies ADD CONSTRAINT FK_148784BB67433D9C FOREIGN KEY (meeting_id) REFERENCES meetings (id)');
        $this->addSql('ALTER TABLE proxies ADD CONSTRAINT FK_148784BB474870EE FOREIGN KEY (principal_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE proxies ADD CONSTRAINT FK_148784BBDB26A4E FOREIGN KEY (proxy_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE proxies');
    }
}
