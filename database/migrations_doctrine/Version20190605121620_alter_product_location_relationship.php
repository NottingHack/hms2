<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190605121620_alter_product_location_relationship extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_vending_location');
        $this->addSql('ALTER TABLE vending_locations ADD product_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE vending_locations ADD CONSTRAINT FK_FB51EA394584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('CREATE INDEX IDX_FB51EA394584665A ON vending_locations (product_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_vending_location (vending_location_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, INDEX IDX_44048F64584665A (product_id), INDEX IDX_44048F6C035A559 (vending_location_id), PRIMARY KEY(vending_location_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_vending_location ADD CONSTRAINT FK_44048F64584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_vending_location ADD CONSTRAINT FK_44048F6C035A559 FOREIGN KEY (vending_location_id) REFERENCES vending_locations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vending_locations DROP FOREIGN KEY FK_FB51EA394584665A');
        $this->addSql('DROP INDEX IDX_FB51EA394584665A ON vending_locations');
        $this->addSql('ALTER TABLE vending_locations DROP product_id');
    }
}
