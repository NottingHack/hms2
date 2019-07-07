<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20161222000655_add_meta extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meta (`key` VARCHAR(255) NOT NULL, `value` VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(`key`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $now = Carbon::now();
        $settings = [
            'access_inner_door' => '1234',
            'access_street_door' => '1234',
            'access_wifi_password' => '123456',
            'access_wifi_ssid' => 'HSNOTTS',
            'accounts_team_email' => 'accounts@nottinghack.org.uk',
            'audit_revoke_interval' => 'P2M',
            'audit_warn_interval' => 'P1M14D',
            'bank_csv_folder' => '/vagrant/storage/app/csv',
            'label_printer_ip' => 'localhost',
            'link_Broken Tools' => 'https://goo.gl/zXpof6',
            'link_Google Group' => 'https://groups.google.com/group/nottinghack?hl=en',
            'link_Hackspace Rules' => 'https://rules.nottinghack.org.uk',
            'link_Induction Request' => 'https://goo.gl/Jl59IM',
            'link_Members Guide' => 'https://guide.nottinghack.org.uk',
            'link_Resources Request' => 'https://docs.google.com/forms/d/1jURztPXwFxh3NbXU0oqdC7T3YfJYK-6x67lq79HAqR8/viewform',
            'link_Team Slack sign up' => 'http://slack.nottinghack.org.uk',
            'link_Wiki' => 'https://wiki.nottinghack.org.uk',
            'membership_email' => 'membership@nottinghack.org.uk',
            'members_guide_html' => 'https://guide.nottinghack.org.uk',
            'members_guide_pdf' => 'https://readthedocs.org/projects/nottingham-hackspace-members-guide/downloads/pdf/latest/',
            'member_box_cost' => '-500',
            'member_box_individual_limit' => '3',
            'member_box_limit' => '129',
            'member_credit_limit' => '2000',
            'purge_cutoff_interval' => 'P6M',
            'rules_html' => 'https://rules.nottinghack.org.uk',
            'software_team_email' => 'software@nottinghack.org.uk',
            'so_accountName' => 'Nottingham Hackspace Ltd',
            'so_accountNumber' => '13007568',
            'so_sortCode' => '77-22-24',
            'trustees_team_email' => 'trustees@nottinghack.org.uk',
        ];

        foreach ($settings as $key => $value) {
            $this->addSql(
                'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'' . $key . '\', \'' . $value . '\', null, \'' . $now . '\', \'' . $now . '\')'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE meta');
    }
}
