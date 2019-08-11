<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190720124431_add_lighting_tables extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lights (id INT UNSIGNED AUTO_INCREMENT NOT NULL, output_channel_id INT UNSIGNED DEFAULT NULL, room_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_38BCB2E8455662C1 (output_channel_id), INDEX IDX_38BCB2E854177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lighting_input_channels (id INT UNSIGNED AUTO_INCREMENT NOT NULL, pattern_id INT UNSIGNED DEFAULT NULL, controller_id INT UNSIGNED DEFAULT NULL, channel INT NOT NULL, statefull TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_3A1926B7F734A20F (pattern_id), INDEX IDX_3A1926B7F6D1A74B (controller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE buildings (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE floors (id INT UNSIGNED AUTO_INCREMENT NOT NULL, building_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, level INT NOT NULL, INDEX IDX_C76687124D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE light_lighting_pattern (light_id INT UNSIGNED NOT NULL, pattern_id INT UNSIGNED NOT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_6AC3221B3DA64B2C (light_id), INDEX IDX_6AC3221BF734A20F (pattern_id), PRIMARY KEY(light_id, pattern_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rooms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, floor_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7CA11A96854679E2 (floor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lighting_output_channels (id INT UNSIGNED AUTO_INCREMENT NOT NULL, controller_id INT UNSIGNED DEFAULT NULL, channel INT NOT NULL, INDEX IDX_8F437396F6D1A74B (controller_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lighting_controllers (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_F015132554177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lighting_patterns (id INT UNSIGNED AUTO_INCREMENT NOT NULL, next_pattern_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, timeout INT DEFAULT NULL, UNIQUE INDEX UNIQ_8AD8F8FF48A349CC (next_pattern_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lights ADD CONSTRAINT FK_38BCB2E8455662C1 FOREIGN KEY (output_channel_id) REFERENCES lighting_output_channels (id)');
        $this->addSql('ALTER TABLE lights ADD CONSTRAINT FK_38BCB2E854177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE lighting_input_channels ADD CONSTRAINT FK_3A1926B7F734A20F FOREIGN KEY (pattern_id) REFERENCES lighting_patterns (id)');
        $this->addSql('ALTER TABLE lighting_input_channels ADD CONSTRAINT FK_3A1926B7F6D1A74B FOREIGN KEY (controller_id) REFERENCES lighting_controllers (id)');
        $this->addSql('ALTER TABLE floors ADD CONSTRAINT FK_C76687124D2A7E12 FOREIGN KEY (building_id) REFERENCES buildings (id)');
        $this->addSql('ALTER TABLE light_lighting_pattern ADD CONSTRAINT FK_6AC3221B3DA64B2C FOREIGN KEY (light_id) REFERENCES lights (id)');
        $this->addSql('ALTER TABLE light_lighting_pattern ADD CONSTRAINT FK_6AC3221BF734A20F FOREIGN KEY (pattern_id) REFERENCES lighting_patterns (id)');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A96854679E2 FOREIGN KEY (floor_id) REFERENCES floors (id)');
        $this->addSql('ALTER TABLE lighting_output_channels ADD CONSTRAINT FK_8F437396F6D1A74B FOREIGN KEY (controller_id) REFERENCES lighting_controllers (id)');
        $this->addSql('ALTER TABLE lighting_controllers ADD CONSTRAINT FK_F015132554177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE lighting_patterns ADD CONSTRAINT FK_8AD8F8FF48A349CC FOREIGN KEY (next_pattern_id) REFERENCES lighting_patterns (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE light_lighting_pattern DROP FOREIGN KEY FK_6AC3221B3DA64B2C');
        $this->addSql('ALTER TABLE floors DROP FOREIGN KEY FK_C76687124D2A7E12');
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A96854679E2');
        $this->addSql('ALTER TABLE lights DROP FOREIGN KEY FK_38BCB2E854177093');
        $this->addSql('ALTER TABLE lighting_controllers DROP FOREIGN KEY FK_F015132554177093');
        $this->addSql('ALTER TABLE lights DROP FOREIGN KEY FK_38BCB2E8455662C1');
        $this->addSql('ALTER TABLE lighting_input_channels DROP FOREIGN KEY FK_3A1926B7F6D1A74B');
        $this->addSql('ALTER TABLE lighting_output_channels DROP FOREIGN KEY FK_8F437396F6D1A74B');
        $this->addSql('ALTER TABLE lighting_input_channels DROP FOREIGN KEY FK_3A1926B7F734A20F');
        $this->addSql('ALTER TABLE light_lighting_pattern DROP FOREIGN KEY FK_6AC3221BF734A20F');
        $this->addSql('ALTER TABLE lighting_patterns DROP FOREIGN KEY FK_8AD8F8FF48A349CC');
        $this->addSql('DROP TABLE lights');
        $this->addSql('DROP TABLE lighting_input_channels');
        $this->addSql('DROP TABLE buildings');
        $this->addSql('DROP TABLE floors');
        $this->addSql('DROP TABLE light_lighting_pattern');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('DROP TABLE lighting_output_channels');
        $this->addSql('DROP TABLE lighting_controllers');
        $this->addSql('DROP TABLE lighting_patterns');
    }
}
