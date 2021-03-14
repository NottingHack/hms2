<?php

namespace HMS\Composers;

use HMS\Entities\User;
use HMS\Helpers\Features;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class NavComposer
{
    /**
     * @var array
     */
    private $navigation = [];

    /**
     * @var Illuminate\Http\Request
     */
    private $request;

    /**
     * @var Features
     */
    protected $features;

    /**
     * @param Request $request
     * @param Features $features
     */
    public function __construct(Request $request, Features $features)
    {
        $this->navigation = config('navigation.main');

        $this->request = $request;
        $this->features = $features;
    }

    /**
     * Is called before views render.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $links = $this->getMainNav();

        $view->with('mainNav', $links);
    }

    /**
     * Gets and array of links for the view.
     *
     * @return array the links
     */
    private function getMainNav()
    {
        // get the current user
        $user = Auth::user();

        return $this->buildLinks($this->navigation, $user);
    }

    /**
     * Iterative function to build the links.
     *
     * @param array $navLinks
     * @param User|null $user
     *
     * @return array   links
     */
    private function buildLinks($navLinks, $user)
    {
        $links = [];

        foreach ($navLinks as $navItem) {
            // check if the current user can access this link
            if (isset($navItem['feature'])
                && $this->features->isDisabled($navItem['feature'])) {
                // check if feature is disabled
                $allowed = false;
            } elseif (count($navItem['permissions']) > 0) {
                $allowed = false;
                foreach ($navItem['permissions'] as $permission) {
                    if (! is_null($user) && $user->can($permission)) {
                        $allowed = true;
                    }
                }
            } else {
                $allowed = true;
            }

            // populate the array if they can
            if ($allowed) {
                $link = [
                    'url'       => '#',
                    'text'      =>  $navItem['text'],
                    'active'    =>  false,
                    'links'     =>  [],
                ];

                if (isset($navItem['route'])) {
                    $link['url'] = route($navItem['route']);
                }

                // is the current route part of this link?
                // multiple routes can set a link as "active"
                if (isset($navItem['match']) && strpos($this->request->url(), route($navItem['match'])) !== false) {
                    $link['active'] = true;
                } elseif (! isset($navItem['match'])) {
                    if ($this->request->url() == $link['url']) {
                        $link['active'] = true;
                    }
                }

                if (isset($navItem['links']) && count($navItem['links']) > 0) {
                    $link['links'] = $this->buildLinks($navItem['links'], $user);

                    foreach ($link['links'] as $subLink) {
                        if ($subLink['active']) {
                            $link['active'] = true;
                        }
                    }
                }

                if (isset($navItem['links']) && count($navItem['links']) > 0 && count($link['links']) == 0) {
                    continue;
                } else {
                    $links[] = $link;
                }
            }
        }

        return $links;
    }
}
