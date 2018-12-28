<?php

namespace HMS\Repositories\Tools;

use HMS\Entities\Tools\Tool;

interface ToolRepository
{
    /**
     * @return Tool[]
     */
    public function findAll();

    /**
     * save Tool to the DB.
     * @param  Tool $tool
     */
    public function save(Tool $tool);

    /**
     * remove Tool from the DB.
     * @param  Tool $tool
     */
    public function remove(Tool $tool);
}
