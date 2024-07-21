<?php

namespace Tests\Unit\HMS\Factories\Tools;

use Tests\TestCase;
use HMS\Factories\Tools\ToolFactory;

class ToolFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function test_create_assigns_the_tool_name()
    {
        $tool = $this->factoryCreate();

        $this->assertEquals('Tool Name', $tool->getName());
    }

    /**
     * @return void
     */
    public function test_create_sets_the_hidden_property()
    {
        $tool = $this->factoryCreate();

        $this->assertEquals(false, $tool->isHidden());
    }

    /**
     * @return void
     */
    public function test_create_sets_the_hidden_property_when_hidden()
    {
        $tool = $this->factoryCreate(hidden: true);

        $this->assertEquals(true, $tool->isHidden());
    }

    /**
     * @return HMS\Entities\Tools\Tool
     */
    private function factoryCreate($hidden = false) {
        $factory = new ToolFactory();

        return $factory->create('Tool Name', 'Tool Description', true, 300, 30, 120, 1, $hidden);
    }
}

