<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170530171450_let_doctrine_fix_indexes extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_users DROP FOREIGN KEY FK_E1F35DCBA76ED395');
        $this->addSql('ALTER TABLE email_users DROP FOREIGN KEY FK_E1F35DCBA832C1C9');
        $this->addSql('DROP INDEX idx_e1f35dcba832c1c9 ON email_users');
        $this->addSql('CREATE INDEX IDX_89CEE466A832C1C9 ON email_users (email_id)');
        $this->addSql('DROP INDEX idx_e1f35dcba76ed395 ON email_users');
        $this->addSql('CREATE INDEX IDX_89CEE466A76ED395 ON email_users (user_id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_E1F35DCBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_E1F35DCBA832C1C9 FOREIGN KEY (email_id) REFERENCES emails (id)');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_6123F82085D15B53');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_6123F820A76ED395');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_6123F820E660F4B0');
        $this->addSql('DROP INDEX idx_6123f820a76ed395 ON role_updates');
        $this->addSql('CREATE INDEX IDX_F985B6CBA76ED395 ON role_updates (user_id)');
        $this->addSql('DROP INDEX idx_6123f820e660f4b0 ON role_updates');
        $this->addSql('CREATE INDEX IDX_F985B6CBE660F4B0 ON role_updates (added_role_id)');
        $this->addSql('DROP INDEX idx_6123f82085d15b53 ON role_updates');
        $this->addSql('CREATE INDEX IDX_F985B6CB85D15B53 ON role_updates (removed_role_id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F82085D15B53 FOREIGN KEY (removed_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F820A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_6123F820E660F4B0 FOREIGN KEY (added_role_id) REFERENCES roles (id)');
        $this->addSql('DROP INDEX uniq_4b656c7e8a90aba9 ON vimbadmin_tokens');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8DCFB55B4E645A7E ON vimbadmin_tokens (`key`)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_users DROP FOREIGN KEY FK_89CEE466A832C1C9');
        $this->addSql('ALTER TABLE email_users DROP FOREIGN KEY FK_89CEE466A76ED395');
        $this->addSql('DROP INDEX idx_89cee466a832c1c9 ON email_users');
        $this->addSql('CREATE INDEX IDX_E1F35DCBA832C1C9 ON email_users (email_id)');
        $this->addSql('DROP INDEX idx_89cee466a76ed395 ON email_users');
        $this->addSql('CREATE INDEX IDX_E1F35DCBA76ED395 ON email_users (user_id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_89CEE466A832C1C9 FOREIGN KEY (email_id) REFERENCES emails (id)');
        $this->addSql('ALTER TABLE email_users ADD CONSTRAINT FK_89CEE466A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_F985B6CBA76ED395');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_F985B6CBE660F4B0');
        $this->addSql('ALTER TABLE role_updates DROP FOREIGN KEY FK_F985B6CB85D15B53');
        $this->addSql('DROP INDEX idx_f985b6cba76ed395 ON role_updates');
        $this->addSql('CREATE INDEX IDX_6123F820A76ED395 ON role_updates (user_id)');
        $this->addSql('DROP INDEX idx_f985b6cbe660f4b0 ON role_updates');
        $this->addSql('CREATE INDEX IDX_6123F820E660F4B0 ON role_updates (added_role_id)');
        $this->addSql('DROP INDEX idx_f985b6cb85d15b53 ON role_updates');
        $this->addSql('CREATE INDEX IDX_6123F82085D15B53 ON role_updates (removed_role_id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_F985B6CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_F985B6CBE660F4B0 FOREIGN KEY (added_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE role_updates ADD CONSTRAINT FK_F985B6CB85D15B53 FOREIGN KEY (removed_role_id) REFERENCES roles (id)');
        $this->addSql('DROP INDEX uniq_8dcfb55b4e645a7e ON vimbadmin_tokens');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B656C7E8A90ABA9 ON vimbadmin_tokens (`key`)');
    }
}
