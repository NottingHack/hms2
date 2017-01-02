<?php

namespace HMS\Entities;

use Carbon\Carbon;
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
     * @var string
     */
    protected $description;

    // /**
    //  * @param string $name
    //  * @param string $link
    //  * @param string $description
    //  */
    // public function create($name, $link, $description = null)
    // {
    //     $this->name = $name;
    //     $this->link = $link;
    //     $this->description = $description;
    //     $now = Carbon::now();
    //     $this->setCreatedAt($now);
    //     $this->setUpdatedAt($now);

    //     return $this;
    // }

    /**
     * Static function to instantiate a new Link from given params.
     *
     * @param string $name
     * @param string $link
     * @param string $description
     */
    static public function create($name, $link, $description = null)
    {
        $_link = new static();
        $_link->name = $name;
        $_link->link = $link;
        $_link->description = $description;
        $now = Carbon::now();
        $_link->setCreatedAt($now);
        $_link->setUpdatedAt(clone $now);
        return $_link;
    }

    /**
     * Static function to instantiate a new Link from a LinkRequest.
     *
     * @param LinkRequest $request
     */
    static public function createFromRequest(LinkRequest $request)
    {
        $_link = new static();
        $_link->name = $request['name'];
        $_link->link = $request['link'];
        $_link->description = $request['description'];
        $now = Carbon::now();
        $_link->setCreatedAt($now);
        $_link->setUpdatedAt(clone $now);
        return $_link;
    }

    /**
     * Update with detials from the LinkRequest.
     * @param LinkRequest $request
     */
    public function updateWithRequest(LinkRequest $request)
    {
        $this->name = $request['name'];
        $this->link = $request['link'];
        $this->description = $request['description'];

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
    protected function setName($name)
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
    protected function setLink($link)
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
    protected function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
