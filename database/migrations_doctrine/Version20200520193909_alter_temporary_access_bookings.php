<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20200520193909_alter_temporary_access_bookings extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE temporary_access_bookings ADD bookable_area_id INT UNSIGNED DEFAULT NULL AFTER color, ADD approved TINYINT(1) DEFAULT \'0\' NOT NULL AFTER notes, ADD approved_by_id INT UNSIGNED DEFAULT NULL AFTER approved');
        $this->addSql('ALTER TABLE temporary_access_bookings ADD CONSTRAINT FK_DFC09B0BBD9A6AEE FOREIGN KEY (bookable_area_id) REFERENCES bookable_areas (id)');
        $this->addSql('ALTER TABLE temporary_access_bookings ADD CONSTRAINT FK_DFC09B0B2D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DFC09B0BBD9A6AEE ON temporary_access_bookings (bookable_area_id)');
        $this->addSql('CREATE INDEX IDX_DFC09B0B2D234F6A ON temporary_access_bookings (approved_by_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE temporary_access_bookings DROP FOREIGN KEY FK_DFC09B0BBD9A6AEE');
        $this->addSql('ALTER TABLE temporary_access_bookings DROP FOREIGN KEY FK_DFC09B0B2D234F6A');
        $this->addSql('DROP INDEX IDX_DFC09B0BBD9A6AEE ON temporary_access_bookings');
        $this->addSql('DROP INDEX IDX_DFC09B0B2D234F6A ON temporary_access_bookings');
        $this->addSql('ALTER TABLE temporary_access_bookings DROP bookable_area_id, DROP approved_by_id, DROP approved');
    }
}
