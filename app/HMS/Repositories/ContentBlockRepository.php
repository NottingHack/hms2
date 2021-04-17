<?php

namespace HMS\Repositories;

use HMS\Entities\ContentBlock;

interface ContentBlockRepository
{
    /**
     * Determine if the given setting value exists.
     *
     * @param string $view
     * @param string $block
     *
     * @return bool
     */
    public function has(string $view, string $block);

    /**
     * Get the specified setting value.
     *
     * @param string $view
     * @param string $block
     * @param string $default
     *
     * @return string
     */
    public function get(string $view, string $block, string $default = '');

    /**
     * Save ContentBlock to the DB.
     *
     * @param ContentBlock $content
     */
    public function save(ContentBlock $content);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
