<?php

namespace App\Events\Labels;

use Illuminate\Queue\SerializesModels;

class ManualPrint implements LabelPrintEventInterface
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
     * @return void
     */
    public function __construct(
        string $templateName,
        $substitutions = [],
        $copiesToPrint = 1
    ) {
        $this->templateName = $templateName;
        $this->substitutions = $substitutions;
        $this->copiesToPrint = $copiesToPrint;
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
