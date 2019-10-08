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

namespace Chevere\Controller\Traits;

trait ControllerStringAccessTrait
{
    /** @var string A string representing a ControllerContract name */
    private $controller;

    public function hasControllerString(): bool
    {
        return isset($this->controller);
    }

    public function controllerString(): string
    {
        return $this->controller;
    }
}
