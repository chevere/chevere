<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controller\Traits;

trait ControllerAccessTrait
{
    /** @var string A string representing a ControllerContract name */
    private $controller;

    public function hasController(): bool
    {
        return isset($this->controller);
    }

    public function controller(): string
    {
        return $this->controller;
    }
}
