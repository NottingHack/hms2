<?php

namespace HMS\Entities;

class ContentBlock
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $block;

    /**
     * @var null|string
     */
    protected $content;

    /**
     * ContentBlock constructor.
     */
    public function __construct()
    {
        $this->type = ContentBlockType::PAGE;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return ContentBlockType::TYPE_STRINGS[$this->type];
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return self
     */
    public function setView(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param string $block
     *
     * @return self
     */
    public function setBlock(string $block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param null|string $content
     *
     * @return self
     */
    public function setContent(?string $content)
    {
        $this->content = $content;

        return $this;
    }
}
