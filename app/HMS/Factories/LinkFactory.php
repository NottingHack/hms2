<?php

namespace HMS\Factories;

use Carbon\Carbon;
use HMS\Entities\Link;
use App\Http\Requests\LinkRequest;

class LinkFactory
{
    /**
     * Function to instantiate a new Link from given params.
     *
     * @param string $name
     * @param string $link
     * @param string $description
     *
     * @return Link
     */
    public function create($name, $link, $description = null)
    {
        $_link = new Link();
        $_link->setName($name);
        $_link->setLink($link);
        $_link->setDescription($description);
        $now = Carbon::now();
        $_link->setCreatedAt($now);
        $_link->setUpdatedAt(clone $now);

        return $_link;
    }

    /**
     * Function to instantiate a new Link from a LinkRequest.
     *
     * @param LinkRequest $request
     *
     * @return Link
     */
    public function createFromRequest(LinkRequest $request)
    {
        $_link = new Link();
        $_link->setName($request['name']);
        $_link->setLink($request['link']);
        $_link->setDescription($request['description']);
        $now = Carbon::now();
        $_link->setCreatedAt($now);
        $_link->setUpdatedAt(clone $now);

        return $_link;
    }
}
