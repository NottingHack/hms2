<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20170209091949_populate_starter_links extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $now = Carbon::now();
        $links = [
            ['Broken Tools', 'https://goo.gl/zXpof6', 'Form for reporting broken tools'],
            ['Google Group', 'https://groups.google.com/group/nottinghack?hl=en', 'Mailing list to get in touch with other members'],
            ['Hackspace Rules', 'https://rules.nottinghack.org.uk', 'The Hackspace Rules'],
            ['Induction Request', 'https://goo.gl/Jl59IM', 'Form to request a tool indcution'],
            ['Members Guide', 'https://guide.nottinghack.org.uk', 'The Hackspace Memmbers guide'],
            ['Resources Request', 'https://docs.google.com/forms/d/1jURztPXwFxh3NbXU0oqdC7T3YfJYK-6x67lq79HAqR8/viewform', 'Resources requetst form'],
            ['Team Slack sign up', 'http://slack.nottinghack.org.uk', 'Slack for Hackspace teams'],
            ['Wiki', 'https://wiki.nottinghack.org.uk', 'Hackspace wiki'],
        ];

        foreach ($links as $link) {
            list($name, $url, $description) = $link;
            $this->addSql(
                'INSERT INTO links (name, link, description, deleted_at, created_at, updated_at) VALUES (\''.$name.'\', \''.$url.'\', \''.$description.'\', null, \''.$now.'\', \''.$now.'\')'
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
