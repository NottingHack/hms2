<?php

namespace App\Listeners;

use HMS\Repositories\MetaRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use HMS\Repositories\LabelTemplateRepository;
use App\Events\Labels\LabelPrintEventInterface;

class PrintLabelSubscriber implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * printer port.
     * @var  int
     */
    private $port = 9100;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * LabelTemplate Reposistry.
     * @var LabelTemplateRepository
     */
    protected $labelTemplateRepository;

    /**
     * Create event listner.
     * @param LabelTemplateRepository $labelTemplateRepository
     * @param MetaRepository          $metaRepository
     */
    public function __construct(
        LabelTemplateRepository $labelTemplateRepository,
        MetaRepository $metaRepository
    ) {
        $this->metaRepository = $metaRepository;
        $this->labelTemplateRepository = $labelTemplateRepository;
    }

    /**
     * Handle the event.
     *
     * @param  LabelPrintEventInterface $event
     * @return void
     */
    public function handlePrint(LabelPrintEventInterface $event)
    {
        for ($i = 0; $i < $event->getCopiesToPrint(); $i++) {
            $this->printLabel(
                $event->getTemplateName(),
                $event->getSubstitutions()
            );
            // pause between print jobs so we don't swamp the connection.
            sleep(1);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Labels\ManualPrint',
            'App\Listeners\PrintLabelSubscriber@handlePrint'
        );

        $events->listen(
            'App\Events\Labels\ProjectPrint',
            'App\Listeners\PrintLabelSubscriber@handlePrint'
        );

        $events->listen(
            'App\Events\Labels\BoxPrint',
            'App\Listeners\PrintLabelSubscriber@handlePrint'
        );
    }

    /**
     * Print a label.
     * thanks to http://stackoverflow.com/a/15956807o .
     *
     * @param string $templateName
     * @param array $substitutions
     * @return bool
     */
    private function printLabel($templateName, $substitutions = [])
    {
        $template = $this->labelTemplateRepository->findOneByTemplateName($templateName);
        if ($template == null) {
            return false;
        }
        $template = $template->getTemplate();

        $label = $this->renderTemplate($template, $substitutions);

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return false;
        }

        $result = socket_connect($socket, $this->getHost(), $this->port);
        if ($result === false) {
            return false;
        }

        socket_write($socket, $label, strlen($label));
        socket_close($socket);

        return true;
    }

    /**
     * Render Blade template with given substitutions.
     * borrowed from https://laracasts.com/index.php/discuss/channels/general-discussion/render-template-from-blade-template-in-database .
     *
     * @param  string $template blade template to render
     * @param  array $substitutions
     * @return string
     */
    private function renderTemplate($template, $substitutions)
    {
        $__php = \Blade::compileString($template);
        $obLevel = ob_get_level();
        ob_start();
        extract($substitutions, EXTR_SKIP);
        try {
            eval('?' . '>' . $__php);
        } catch (\Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw new FatalThrowableError($e);
        }

        return ob_get_clean();
    }

    /**
     * Get the IP of the label printer.
     * @return string
     */
    public function getHost()
    {
        // Get the IP address for the printer.
        return $this->metaRepository->get('label_printer_ip');
    }
}
