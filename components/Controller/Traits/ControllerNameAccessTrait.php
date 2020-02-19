<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controller\Traits;

trait ControllerNameAccessTrait
{
    /** @var string A string representing a ControllerInterface name */
    private string $controllerName;

    public function hasControllerName(): bool
    {
        return isset($this->controllerName);
    }

    public function controllerName(): string
    {
        return $this->controllerName;
    }
}
