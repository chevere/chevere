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

namespace Chevere\Router\Interfaces\Route;

use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Stringable;

/**
 * Describes the component in charge of handling route paths.
 */
interface RoutePathInterface extends Stringable
{
    /**
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __construct(string $route);

    /**
     * Provides access to the wildcards instance.
     */
    public function wildcards(): RouteWildcardsInterface;

    /**
     * Provides access to the regex instance.
     */
    public function regex(): RegexInterface;
}
