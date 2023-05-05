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

namespace Chevere\HttpController;

use Chevere\Controller\ControllerName;
use Chevere\Controller\Interfaces\ControllerNameInterface;
use Chevere\HttpController\Interfaces\HttpControllerInterface;
use Chevere\HttpController\Interfaces\HttpControllerNameInterface;

final class HttpControllerName implements HttpControllerNameInterface
{
    private ControllerNameInterface $controllerName;

    public function __construct(string $name)
    {
        $this->controllerName = new ControllerName($name);
        $this->controllerName->assertInterface(HttpControllerInterface::class);
    }

    public function __toString(): string
    {
        /**
         * @var class-string HttpControllerInterface
         */
        return $this->controllerName->__toString();
    }
}
