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

namespace Chevere\Interfaces\Router\Route;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of handling route paths.
 */
interface RoutePathInterface extends ToStringInterface
{
    /**
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __construct(string $route);

    /**
     * Provides access to the wildcards instance.
     */
    public function wildcards(): WildcardsInterface;

    /**
     * Provides access to the regex instance.
     */
    public function regex(): RegexInterface;
}
