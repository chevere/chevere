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

namespace Chevere\Interfaces\Router;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\To\ToArrayInterface;

/**
 * Describes the component in charge of describing the route identifier.
 */
interface RouteIdentifierInterface extends ToArrayInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $group, string $name);

    /**
     * Provides access to the `$group` instance.
     */
    public function group(): string;

    /**
     * Provides access to the `$name` instance.
     */
    public function name(): string;
}
