<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260727152913_add_phone_extensions extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE phone_extensions (extension VARCHAR(8) NOT NULL, user_id INT UNSIGNED DEFAULT NULL, phoneword VARCHAR(8) DEFAULT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(16) NOT NULL, category VARCHAR(16) NOT NULL, sip_password VARCHAR(32) DEFAULT NULL, mapped_number VARCHAR(12) DEFAULT NULL, hidden TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_FDFB5A3CA76ED395 (user_id), UNIQUE INDEX duplicate_idx (extension), PRIMARY KEY(extension)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE phone_extensions ADD CONSTRAINT FK_FDFB5A3CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE phone_extensions DROP FOREIGN KEY FK_FDFB5A3CA76ED395');
        $this->addSql('DROP TABLE phone_extensions');
    }
}
