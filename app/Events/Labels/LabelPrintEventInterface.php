<?php

namespace App\Events\Labels;

interface LabelPrintEventInterface
{
    /**
     * Gets the value of templateName.
     *
     * @return string
     */
    public function getTemplateName();

    /**
     * Gets the value of substitutions.
     *
     * @return array
     */
    public function getSubstitutions();

    /**
     * Gets the value of copiesToPrint.
     *
     * @return int
     */
    public function getCopiesToPrint();
}
