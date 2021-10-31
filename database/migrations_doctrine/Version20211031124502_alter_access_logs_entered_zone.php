<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20211031124502_alter_access_logs_entered_zone extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_logs ADD entered_zone_id INT DEFAULT NULL AFTER door_id');
        $this->addSql('ALTER TABLE access_logs ADD CONSTRAINT FK_656A05A35D9DA3D FOREIGN KEY (entered_zone_id) REFERENCES zones (id)');
        $this->addSql('CREATE INDEX IDX_656A05A35D9DA3D ON access_logs (entered_zone_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE access_logs DROP FOREIGN KEY FK_656A05A35D9DA3D');
        $this->addSql('DROP INDEX IDX_656A05A35D9DA3D ON access_logs');
        $this->addSql('ALTER TABLE access_logs DROP entered_zone_id');
    }
}
