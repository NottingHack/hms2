<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919000931_add_forms extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE forms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, json_definition JSON NOT NULL COMMENT \'(DC2Type:json)\', max_responses INT UNSIGNED DEFAULT 0 NOT NULL, notification_key VARCHAR(255) DEFAULT NULL, permission_name VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forms_response (id INT UNSIGNED AUTO_INCREMENT NOT NULL, form_id INT UNSIGNED DEFAULT NULL, responder_id INT UNSIGNED DEFAULT NULL, response_json JSON NOT NULL COMMENT \'(DC2Type:json)\', comment VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_34DA12C95FF69B7D (form_id), INDEX IDX_34DA12C937395ADB (responder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forms_response ADD CONSTRAINT FK_34DA12C95FF69B7D FOREIGN KEY (form_id) REFERENCES forms (id)');
        $this->addSql('ALTER TABLE forms_response ADD CONSTRAINT FK_34DA12C937395ADB FOREIGN KEY (responder_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forms_response DROP FOREIGN KEY FK_34DA12C95FF69B7D');
        $this->addSql('ALTER TABLE forms_response DROP FOREIGN KEY FK_34DA12C937395ADB');
        $this->addSql('DROP TABLE forms');
        $this->addSql('DROP TABLE forms_response');
    }
}
