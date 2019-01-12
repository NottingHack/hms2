<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190112025057_add_zone_occupancy_tables extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE zone_occupancy_logs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, zone_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, time_exited DATETIME DEFAULT NULL, time_entered DATETIME DEFAULT NULL, INDEX IDX_59ADE3689F2C3FAB (zone_id), INDEX IDX_59ADE368A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone_occupants (user_id INT UNSIGNED NOT NULL, zone_id INT UNSIGNED DEFAULT NULL, time_entered DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_EECE6E0B9F2C3FAB (zone_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zone_occupancy_logs ADD CONSTRAINT FK_59ADE3689F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE zone_occupancy_logs ADD CONSTRAINT FK_59ADE368A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE zone_occupants ADD CONSTRAINT FK_EECE6E0BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE zone_occupants ADD CONSTRAINT FK_EECE6E0B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zones (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE zone_occupancy_logs');
        $this->addSql('DROP TABLE zone_occupants');
    }
}
