<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\ContentBlock;
use HMS\Repositories\ContentBlockRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineContentBlockRepository extends EntityRepository implements ContentBlockRepository
{
    use PaginatesFromRequest;

    /**
     * Determine if the given setting value exists.
     *
     * @param string $view
     * @param string $block
     *
     * @return bool
     */
    public function has(string $view, string $block)
    {
        return parent::findOneBy(['view' => $view, 'block' => $block]) ? true : false;
    }

    /**
     * Get the specified setting value.
     *
     * @param string $view
     * @param string $block
     * @param string $default
     *
     * @return string
     */
    public function get(string $view, string $block, string $default = '')
    {
        $content = parent::findOneBy(['view' => $view, 'block' => $block]);

        return is_null($content) ? $default : $content->getContent();
    }

    /**
     * Save ContentBlock to the DB.
     *
     * @param ContentBlock $content
     */
    public function save(ContentBlock $content)
    {
        $this->_em->persist($content);
        $this->_em->flush();
    }
}
