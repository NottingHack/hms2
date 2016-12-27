<?php

namespace HMS\Helpers;

use HMS\Entities\LabelTemplate;
use HMS\Repositories\MetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class LabelPrinter
{
    /**
     * printer port
     * @var  int
     */
    private $port = 9100;

    /**
     * Ip for the printer
     * @var string
     */
    private $host;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * generic LabelTemplate Reposistry
     * @var ObjectRepository
     */
    protected $labelTemplateGenericRepository;

    public function __construct(EntityManagerInterface $em, MetaRepository $metaRepository)
    {
        $this->em = $em;
        $this->metaRepository = $metaRepository;
        $this->labelTemplateGenericRepository = $em->getRepository(LabelTemplate::class);

        // Get the IP address for the printer.
        // TODO: we need meta to get this ip from
        $this->host = $this->metaRepository->get('label_printer_ip');

    }

    /**
     * Print a label
     * thanks to http://stackoverflow.com/a/15956807
     *
     * @param string $templateName
     * @param array $substitutions
     * @return bool
     */
    public function printLabel($templateName, $substitutions = array()) {

        $template = $this->labelTemplateGenericRepository->find($templateName)->getTemplate();
        if ($template == null) {
            return false;
        }

        $label = $this->renderTemplate($template, $substitutions);

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return false;
        }

        $result = socket_connect($socket, $this->host, $this->port);
        if ($result === false) {
            return false;
        }

        socket_write($socket, $label, strlen($label));
        socket_close($socket);

        return true;
    }

    /**
     * Render Blade template with given substitutions
     * borrowed from https://laracasts.com/index.php/discuss/channels/general-discussion/render-template-from-blade-template-in-database
     * @param  string $template blade template to render
     * @param  [type] $substitutions [description]
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
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new FatalThrowableError($e);
        }
        return ob_get_clean();
    }
}
