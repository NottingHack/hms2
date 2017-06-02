<?php

namespace Database\Migrations;

use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

class Version20161220211524_add_label_template extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE label_templates (template_name VARCHAR(200) NOT NULL, template LONGTEXT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(template_name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $now = Carbon::now();

        $this->addSql('INSERT INTO label_templates (template_name, template, created_at, updated_at) VALUES
            (\'member_box\',\'N\r\nq792\r\nA40,5,0,4,3,3,N,"MEMBER\'\'S BOX"\r\n\r\n;General info\r\nA10,90,0,4,1,1,N,"Member Name:"\r\nA10,130,0,4,2,2,N,"{{ $memberName }}"\r\nA10,230,0,4,1,1,N,"Member Username:"\r\nA10,270,0,4,2,2,N,"{{ $username }}"\r\n\r\n;qrcode and project Id\r\nb10,370,Q,s6,"{{ $qrURL }}"\r\nA220,370,0,4,1,1,N,"Box Id:"\r\nA{{ $idOffset }},455,0,4,2,2,N,"{{ $memberBoxId }}"\r\n\r\nP1\r\n\', \''.$now.'\', \''.$now.'\'),
            (\'member_project\', \'N\r\nq792\r\nA40,5,0,4,3,3,N,"DO NOT HACK"\r\n\r\n;General info\r\nA10,90,0,4,1,1,N,"Project name:"\r\nA10,130,0,4,1,1,N,"{{ $projectName }}"\r\nA10,170,0,4,1,1,N,"Member Name:"\r\nA10,210,0,4,1,1,N,"{{ $memberName }}"\r\nA10,250,0,4,1,1,N,"Member Username:"\r\nA10,290,0,4,1,1,N,"{{ $username }}"\r\nA10,330,0,4,1,1,N,"Start date: {{ $startDate }}"\r\n\r\n;Worked on box\r\nLO600,5,176,4\r\nLO600,45,176,2\r\nLO600,5,4,563\r\nLO776,5,4,563\r\nLO600,568,176,4\r\nA610,15,0,4,1,1,N,"Worked on"\r\nA610,55,0,3,1,1,N,"{{ $lastDate }}"\r\n\r\n;qrcode and project Id\r\nb10,370,Q,s6,"{{ $qrURL }}"\r\nA220,370,0,4,1,1,N,"Project Id:"\r\nA{{ $idOffset }},455,0,4,2,2,N,"{{ $memberProjectId }}"\r\n\r\nP1\r\n\', \''.$now.'\', \''.$now.'\')'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE label_templates');
    }
}
