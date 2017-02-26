<?php

namespace App\Listeners\Labels;

use HMS\Helpers\LabelPrinter;
use App\Events\Labels\ManualPrint;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrintManualLabel implements ShouldQueue
{
    /**
     * @var LabelPrinter
     */
    protected $labelPrinter;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(LabelPrinter $labelPrinter)
    {
        //
        $this->labelPrinter = $labelPrinter;
    }

    /**
     * Handle the event.
     *
     * @param  ManualPrint  $event
     * @return void
     */
    public function handle(ManualPrint $event)
    {
        for ($i = 0; $i < $event->copiesToPrint; $i++) {
            $this->labelPrinter->printLabel(
                $event->templateName,
                $event->substitutions
            );
            sleep(1);
        }
    }
}
