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

namespace Chevere\Components\Route;

use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardsInterface;
use Chevere\Components\Route\RouteWildcards;
use ReflectionClass;

abstract class RouteDecorator implements RouteDecoratorInterface
{
    /** @var string Absolute path to the decorator file */
    private string $whereIs;

    abstract public function name(): RouteNameInterface;

    public function wildcards(): RouteWildcardsInterface
    {
        return new RouteWildcards(); // @codeCoverageIgnore
    }

    final public function whereIs(): string
    {
        return $this->whereIs ??= (new ReflectionClass($this))->getFileName();
    }
}
