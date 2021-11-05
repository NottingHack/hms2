<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170512233938_add_emails_table extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE emails (id INT UNSIGNED AUTO_INCREMENT NOT NULL, role_id INT UNSIGNED DEFAULT NULL, to_address LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, INDEX IDX_4C81E852D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_users (email_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_89CEE466A832C1C9 (email_id), INDEX IDX_89CEE466A76ED395 (user_id), PRIMARY KEY(email_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emails ADD CONSTRAINT FK_4C81E852D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_89CEE466A832C1C9 FOREIGN KEY (email_id) REFERENCES emails (id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_89CEE466A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_users DROP FOREIGN KEY FK_89CEE466A832C1C9');
        $this->addSql('DROP TABLE emails');
        $this->addSql('DROP TABLE email_users');
    }
}
