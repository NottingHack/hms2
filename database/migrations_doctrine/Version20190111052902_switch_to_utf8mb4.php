<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190111052902_switch_to_utf8mb4 extends AbstractMigration
{
    protected $tables = [
        'permissions',
        'meta',
        'email_users',
        'blacklist_usernames',
        'member_boxes',
        'role_updates',
        'member_projects',
        'emails',
        'accounts',
        'role_user',
        'access_logs',
        'roles',
        'doors',
        'label_templates',
        'links',
        'profile',
        'migrations_doctrine',
        'invites',
        'bank_transactions',
        'rfid_tags',
        'zones',
        'membership_status_notifications',
        'transactions',
        'permission_role',
        'password_resets',
        'user',
        'pins',
        'vimbadmin_tokens',
        'banks',
        'products',
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        foreach ($this->tables as $tbl_name) {
            $this->addSql("ALTER TABLE $tbl_name DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->addSql("ALTER TABLE $tbl_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        foreach ($this->tables as $tbl_name) {
            $this->addSql("ALTER TABLE $tbl_name DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
            $this->addSql("ALTER TABLE $tbl_name CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
        }
    }
}
