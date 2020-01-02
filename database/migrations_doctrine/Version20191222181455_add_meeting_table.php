<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20191222181455_add_meeting_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meetings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, start_time DATETIME NOT NULL, extraordinary TINYINT(1) DEFAULT \'0\' NOT NULL, current_members INT DEFAULT 0 NOT NULL, voting_members INT DEFAULT 0 NOT NULL, quorum INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting_user (meeting_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_61622E9B67433D9C (meeting_id), INDEX IDX_61622E9BA76ED395 (user_id), PRIMARY KEY(meeting_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meeting_user ADD CONSTRAINT FK_61622E9B67433D9C FOREIGN KEY (meeting_id) REFERENCES meetings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meeting_user ADD CONSTRAINT FK_61622E9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meeting_user DROP FOREIGN KEY FK_61622E9B67433D9C');
        $this->addSql('DROP TABLE meetings');
        $this->addSql('DROP TABLE meeting_user');
    }
}
