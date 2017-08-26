<?php

namespace App\Events\Labels;

use Carbon\Carbon;
use HMS\Entities\Members\Project;
use Illuminate\Queue\SerializesModels;

class ProjectPrint implements LabelPrintEventInterface
{
    use SerializesModels;

    /**
     * @var string
     */
    public $templateName;

    /**
     * @var array
     */
    public $substitutions;

    /**
     * @var int
     */
    public $copiesToPrint;

    /**
     * Create a new event instance.
     *
     * @param Project $project
     * @param int $copiesToPrint
     */
    public function __construct(Project $project,
        $copiesToPrint = 1)
    {
        $this->templateName = 'member_project';
        $this->copiesToPrint = $copiesToPrint;

        // hack to offset the ID printing and give the look of right justification
        $idOffset = (5 - strlen($project->getId())) * 35;

        $this->substitutions = [
            'projectName' => $project->getProjectName(),
            'memberName' => $project->getUser()->getFullname(),
            'username' => $project->getUser()->getUsername(),
            'startDate' => $project->getStartDate()->toDateString(),
            'lastDate' => Carbon::now()->toDateString(),
            'qrURL' => route('projects.show', $project->getId()),
            'idOffset' => $idOffset,
            'memberProjectId' => $project->getId(),
        ];
    }

    /**
     * Gets the value of templateName.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Gets the value of substitutions.
     *
     * @return array
     */
    public function getSubstitutions()
    {
        return $this->substitutions;
    }

    /**
     * Gets the value of copiesToPrint.
     *
     * @return int
     */
    public function getCopiesToPrint()
    {
        return $this->copiesToPrint;
    }
}
