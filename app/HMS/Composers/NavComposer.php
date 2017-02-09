<?php

namespace App\HMS\Composers;

use Illuminate\View\View;

class NavComposer
{

    private $navigation = [];

    public function __construct()
    {
        $this->navigation = config('navigation.main');;
    }

    public function compose(View $view)
    {
        $links = $this->getMainNav();

        $view->with('mainNav', $links);
    }

    private function getMainNav()
    {
        return $this->buildLinks($this->navigation);
    }

    private function buildLinks($navLinks)
    {
        $links = [];

        foreach ($navLinks as $navItem) {
            $link = [
                'url'   =>  route($navItem['route']),
                'text'  =>  $navItem['text'],
                'links' =>  [],
            ];

            if (count($navItem['links']) > 0) {
                $link['links'] = $this->buildLinks($navItem['links']);
            }

            $links[] = $link;
        }

        return $links;
    }
}
