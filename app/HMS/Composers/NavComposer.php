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
        return $this->navigation;
    }
}
