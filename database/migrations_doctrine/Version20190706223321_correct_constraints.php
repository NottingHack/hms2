<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190706223321_correct_constraints extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pins DROP INDEX IDX_3F0FE980A76ED395, ADD UNIQUE INDEX UNIQ_3F0FE980A76ED395 (user_id)');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A70C827BA');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A49451B7F');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A49451B7F FOREIGN KEY (side_a_zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A70C827BA FOREIGN KEY (side_b_zone_id) REFERENCES zones (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A70C827BA');
        $this->addSql('ALTER TABLE doors DROP FOREIGN KEY FK_5E5B762A49451B7F');
        $this->addSql('ALTER TABLE doors ADD CONSTRAINT FK_5E5B762A49451B7F FOREIGN KEY (side_b_zone_id) REFERENCES zones (id)');
        $this->addSql('ALTER TABLE pins DROP INDEX UNIQ_3F0FE980A76ED395, ADD INDEX IDX_3F0FE980A76ED395 (user_id)');
    }
}
