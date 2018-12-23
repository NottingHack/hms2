<?php

namespace App\Events\Labels;

use Carbon\Carbon;
use HMS\Entities\Members\Box;
use Illuminate\Queue\SerializesModels;

class BoxPrint implements LabelPrintEventInterface
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
     * @param Box $box
     * @param int $copiesToPrint
     */
    public function __construct(Box $box,
        $copiesToPrint = 1)
    {
        $this->templateName = 'member_box';
        $this->copiesToPrint = $copiesToPrint;

        // hack to offset the ID printing and give the look of right justification
        $idOffset = (5 - strlen($box->getId())) * 35;

        $this->substitutions = [
            'memberName' => $box->getUser()->getFullname(),
            'username' => $box->getUser()->getUsername(),
            'lastDate' => Carbon::now()->toDateString(),
            'qrURL' => route('user.boxes', ['user' => $box->getUser()->getId()]),
            'idOffset' => 220 + $idOffset,
            'memberBoxId' => $box->getId(),
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
