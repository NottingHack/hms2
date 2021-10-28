<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190808203208_alter_stripe_charges_missing_user extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_charges ADD user_id INT UNSIGNED DEFAULT NULL AFTER id ');
        $this->addSql('ALTER TABLE stripe_charges ADD CONSTRAINT FK_152861E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_152861E0A76ED395 ON stripe_charges (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_charges DROP FOREIGN KEY FK_152861E0A76ED395');
        $this->addSql('DROP INDEX IDX_152861E0A76ED395 ON stripe_charges');
        $this->addSql('ALTER TABLE stripe_charges DROP user_id');
    }
}
