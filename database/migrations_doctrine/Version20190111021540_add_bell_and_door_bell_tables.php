<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111021540_add_bell_and_door_bell_tables extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bells (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description VARCHAR(100) NOT NULL, topic VARCHAR(100) NOT NULL, message VARCHAR(50) NOT NULL, enabled TINYINT(1) DEFAULT \'1\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE door_bell (door_id INT UNSIGNED NOT NULL, bell_id INT UNSIGNED NOT NULL, INDEX IDX_3D65815658639EAE (door_id), INDEX IDX_3D6581566D4ED28E (bell_id), PRIMARY KEY(door_id, bell_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE door_bell ADD CONSTRAINT FK_3D65815658639EAE FOREIGN KEY (door_id) REFERENCES doors (id)');
        $this->addSql('ALTER TABLE door_bell ADD CONSTRAINT FK_3D6581566D4ED28E FOREIGN KEY (bell_id) REFERENCES bells (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE door_bell DROP FOREIGN KEY FK_3D6581566D4ED28E');
        $this->addSql('DROP TABLE bells');
        $this->addSql('DROP TABLE door_bell');
    }
}
