<?php

namespace HMS\Entities;

use App\Http\Requests\LinkRequest;
use HMS\Traits\Entities\SoftDeletable;
use LaravelDoctrine\ORM\Serializers\Arrayable;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class Link implements ArrayableContract
{
    use SoftDeletable, Timestamps, Arrayable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * Update with detials from the LinkRequest.
     * @param LinkRequest $request
     * @return self
     */
    public function updateWithRequest(LinkRequest $request)
    {
        $this->name = $request['name'];
        $this->link = $request['link'];
        $this->description = $request['description'];

        return $this;
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
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the value of link.
     *
     * @param string $link the link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
