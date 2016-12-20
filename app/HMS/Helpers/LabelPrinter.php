<?php

namespace HMS\Helpers;

use HMS\Entities\LabelTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

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
     * generic LabelTemplate Reposistry
     * @var ObjectRepository
     */
    protected $labelTemplateGenericRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->labelTemplateGenericRepository = $em->getRepository(LabelTemplate::class);

        // Get the IP address for the printer.
        // TODO: we need meta to get this ip from
        // $this->host = $meta->getValueFor('label_printer_ip');
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

        $template = $labelTemplateGenericRepository->find($templateName);
        if ($template == null) {
            return false;
        }

        // TODO: find a replacemnet for CakeText
        // $label = CakeText::insert($template, $substitutions);
        $label = $template;

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
}
