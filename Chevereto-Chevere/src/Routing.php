<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere;

class Routing
{
    /** @var int */
    public $count;

    /** @var string */
    public $type;

    /** @var string */
    public $regex;

    /** @var string */
    public $routeSet;

    /** @var string */
    public $routeSetHandle;

    /** @var Route */
    public $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
        $this->routeSet = $this->route->getSet();
        $this->handleRouteSetHandleRegex($this->routeSet);
        $this->handleType($this->routeSet);
        $this->handleCount($this->routeSetHandle);
    }

    protected function handleRouteSetHandleRegex(?string $routeSet)
    {
        if ($routeSet) {
            $this->routeSetHandle = $routeSet;
            $this->regex = $this->route->regex($routeSet);
        } else {
            $this->routeSetHandle = $routeSet ?? $this->route->getUri();
            $this->regex = $this->route->regex();
        }
    }

    protected function handleType(?string $routeSet)
    {
        if (isset($routeSet)) {
            $this->type = Route::TYPE_STATIC;
        } else {
            if (null != $this->routeSetHandle) {
                $pregReplace = preg_replace('/{[0-9]+}/', '', $this->routeSetHandle);
                if (null != $pregReplace) {
                    $pregReplace = trim(Path::normalize($pregReplace), '/');
                }
            }
            $this->type = isset($pregReplace) ? Route::TYPE_MIXED : Route::TYPE_DYNAMIC;
        }
    }

    protected function handleCount(?string $routeSetHandle)
    {
        if (null != $routeSetHandle) {
            $routeSetHandleTrim = ltrim($routeSetHandle, '/');
            $explode = explode('/', $routeSetHandleTrim);
            $this->count = '/' == $this->route->getUri() ? 0 : count($explode);
        } else {
            $this->count = 0;
        }
    }
}
