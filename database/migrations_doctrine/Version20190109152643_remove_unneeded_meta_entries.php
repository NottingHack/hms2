<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190109152643_remove_unneeded_meta_entries extends AbstractMigration
{
    /**
     * Links we will be removing or adding.
     *
     * @var string[]
     */
    protected $settings = [
        'accounts_team_email' => 'accounts@nottinghack.org.uk',
        'bank_csv_folder' => '/vagrant/storage/app/csv',
        'link_Broken Tools' => 'https://goo.gl/zXpof6',
        'link_Hackspace Rules' => 'https://rules.nottinghack.org.uk',
        'link_Induction Request' => 'https://goo.gl/Jl59IM',
        'link_Members Guide' => 'https://guide.nottinghack.org.uk',
        'link_Resources Request' => 'https://docs.google.com/forms/d/1jURztPXwFxh3NbXU0oqdC7T3YfJYK-6x67lq79HAqR8/viewform',
        'link_Team Slack sign up' => 'http://slack.nottinghack.org.uk',
        'membership_email' => 'membership@nottinghack.org.uk',
        'software_team_email' => 'software@nottinghack.org.uk',
        'so_accountNumber' => '13007568',
        'so_sortCode' => '77-22-24',
        'trustees_team_email' => 'trustees@nottinghack.org.uk',
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        foreach ($this->settings as $key => $value) {
            $this->addSql('DELETE FROM meta WHERE `key` = \'' . $key . '\'');
        }

        $this->addSql('UPDATE meta SET `key` = \'google_group_html\' WHERE `key` = \'link_Google Group\'');
        $this->addSql('UPDATE meta SET `key` = \'wiki_html\' WHERE `key` = \'link_Wiki\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $now = Carbon::now();

        $this->addSql('UPDATE meta SET `key` = \'link_Wiki\' WHERE `key` = \'wiki_html\'');
        $this->addSql('UPDATE meta SET `key` = \'link_Google Group\' WHERE `key` = \'google_group_html\'');

        foreach ($this->settings as $key => $value) {
            $this->addSql(
                'INSERT INTO meta (`key`, `value`, deleted_at, created_at, updated_at) VALUES (\'' . $key . '\', \'' . $value . '\', null, \'' . $now . '\', \'' . $now . '\')'
            );
        }
    }
}
