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

use Chevere\Exceptions\Router\Route\RouteNameInvalidException;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of defining a route name.
 */
interface RouteNameInterface extends ToStringInterface
{
    /**
     * Regex pattern used to match repo:path
     */
    public const REGEX = '/^([\w\-]+)\:(\/.+\/|\/)$/i';

    /**
     * @throws RouteNameInvalidException if `$name` doesn't match `self::REGEX`
     */
    public function __construct(string $name);

    public function repository(): string;

    public function path(): string;
}
